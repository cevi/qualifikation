<?php

namespace Tests\Feature;

use App\Auth\HitobitoProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Login über die Cevi-DB (Hitobito) und die JEMK-DB. Zuordnung über die Cevi-DB-ID, nie über die E-Mail allein.
 */
class HitobitoLoginTest extends TestCase
{
    public function test_new_person_gets_an_account_and_is_logged_in()
    {
        $this->fakeHitobito(['id' => 4711, 'nickname' => 'Fuchs', 'email' => 'fuchs@example.com']);

        $this->get(route('login.hitobito.callback'))->assertRedirect('/home');

        $user = User::where('foreign_id', 4711)->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(['Fuchs', 'fuchs@example.com'], [$user->username, $user->email]);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->slug);
    }

    public function test_returning_person_is_recognised_by_cevi_db_id_and_email_is_updated()
    {
        $user = User::factory()->create(['foreign_id' => 4711, 'email' => 'alt@example.com']);
        $this->fakeHitobito(['id' => 4711, 'nickname' => 'Fuchs', 'email' => 'neu@example.com']);

        $this->get(route('login.hitobito.callback'))->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);
        $this->assertSame('neu@example.com', $user->fresh()->email);
        $this->assertSame(1, User::where('foreign_id', 4711)->count());
    }

    public function test_email_of_another_account_is_not_taken_over()
    {
        $user = User::factory()->create(['foreign_id' => 4711, 'email' => 'fuchs@example.com']);
        $other = User::factory()->create(['email' => 'besetzt@example.com']);
        $this->fakeHitobito(['id' => 4711, 'nickname' => 'Fuchs', 'email' => 'besetzt@example.com']);

        $this->get(route('login.hitobito.callback'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame('fuchs@example.com', $user->fresh()->email);
        $this->assertSame('besetzt@example.com', $other->fresh()->email);
    }

    public function test_existing_local_account_cannot_be_taken_over_via_hitobito()
    {
        $local = User::factory()->create(['email' => 'konto@example.com', 'foreign_id' => null]);
        $this->fakeHitobito(['id' => 999, 'nickname' => 'Jemand', 'email' => 'konto@example.com']);

        $this->get(route('login.hitobito.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('hitobito');

        $this->assertGuest();
        $this->assertNull($local->fresh()->foreign_id);
        $this->assertDatabaseMissing('users', ['foreign_id' => 999]);
    }

    public function test_denied_access_shows_error()
    {
        $this->get(route('login.hitobito.callback', ['error' => 'access_denied']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('hitobito');

        $this->assertGuest();
    }

    public function test_jemk_login_follows_the_same_rules()
    {
        $this->fakeHitobito(['id' => 815, 'nickname' => 'Dachs', 'email' => 'dachs@example.com'], 'hitobito_jemk');

        $this->get(route('login.hitobito_jemk.callback'))->assertRedirect('/home');

        $this->assertAuthenticatedAs(User::where('foreign_id', 815)->firstOrFail());
    }

    /**
     * Name aus dem Cevi-DB-Profil: Nickname, sonst Vorname, sonst Nachname, sonst Teil vor dem @.
     */
    public static function profiles(): array
    {
        return [
            'Nickname' => [['nickname' => 'Fuchs', 'first_name' => 'Anna', 'last_name' => 'Muster'], 'Fuchs'],
            'Vorname' => [['first_name' => 'Anna', 'last_name' => 'Muster'], 'Anna'],
            'Nachname' => [['last_name' => 'Muster'], 'Muster'],
            'E-Mail' => [[], 'anna.muster'],
        ];
    }

    #[DataProvider('profiles')]
    public function test_username_falls_back_through_profile_fields(array $profile, string $expected)
    {
        $user = $this->mapProfile(['id' => 1, 'email' => 'anna.muster@example.com'] + $profile);

        $this->assertSame($expected, $user->getNickname());
    }

    public function test_avatar_uses_profile_picture_or_default()
    {
        $this->assertStringEndsWith('/uploads/person/picture/7/bild.jpg', $this->mapProfile(['id' => 7, 'email' => 'a@example.com', 'picture' => 'bild.jpg'])->getAvatar());
        $this->assertStringContainsString('/packs/media/images/profil-', $this->mapProfile(['id' => 7, 'email' => 'a@example.com'])->getAvatar());
    }

    public static function logins(): array
    {
        return [
            'Cevi-DB' => ['login.hitobito', 'hitobito', ['name', 'with_roles']],
            'JEMK-DB' => ['login.hitobito_jemk', 'hitobito_jemk', ['name']],
        ];
    }

    #[DataProvider('logins')]
    public function test_login_button_redirects_to_the_database_with_its_scopes(string $route, string $driver, array $scopes)
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('setScopes')->once()->with($scopes)->andReturnSelf();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://db.cevi.test/oauth/authorize'));
        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        $this->get(route($route))->assertRedirect('https://db.cevi.test/oauth/authorize');
    }

    private function fakeHitobito(array $profile, string $driver = 'hitobito'): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('setRequest')->andReturnSelf();
        $provider->shouldReceive('setScopes')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn((new SocialiteUser)->map($profile));
        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);
    }

    private function mapProfile(array $profile): SocialiteUser
    {
        $provider = new HitobitoProvider(Request::create('/'), 'https://db.cevi.test', 'client', 'secret', 'https://quali.test/callback');

        return (fn (array $user) => $this->mapUserToObject($user))->call($provider, $profile);
    }
}
