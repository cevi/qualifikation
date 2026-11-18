<?php

namespace Tests\Feature\Security;

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DemoCampSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * PATCH /user/{id}: das eigene Konto auf der Kontoseite (E-Mail und Passwort).
 */
class AccountUpdateTest extends TestCase
{
    public function test_user_cannot_change_password_of_another_user()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)
            ->patch(route('home.update', $s->admin->id), ['password' => 'uebernommen', 'password_confirmation' => 'uebernommen'])
            ->assertForbidden();

        $this->assertFalse(Hash::check('uebernommen', $s->admin->fresh()->password));
    }

    public function test_public_demo_login_cannot_change_password_of_admin()
    {
        $this->seed(DemoCampSeeder::class);
        $this->get('/loginTN');

        $this->patch(route('home.update', 1), ['password' => 'uebernommen', 'password_confirmation' => 'uebernommen'])
            ->assertForbidden();

        $this->assertFalse(Hash::check('uebernommen', User::findOrFail(1)->password));
    }

    public function test_user_cannot_change_role_of_another_user()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)
            ->patch(route('home.update', $s->unassignedParticipant->id), ['role_id' => Role::Administrator->value])
            ->assertForbidden();

        $this->assertFalse($s->unassignedParticipant->fresh()->isAdmin());
    }

    public function test_user_can_change_own_email_and_password()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)
            ->patch(route('home.update', $s->participant->id), [
                'email' => 'neu@example.com',
                'password' => 'neues-passwort',
                'password_confirmation' => 'neues-passwort',
            ])
            ->assertRedirect('/home');

        $participant = $s->participant->fresh();
        $this->assertSame('neu@example.com', $participant->email);
        $this->assertTrue(Hash::check('neues-passwort', $participant->password));
    }

    public function test_user_cannot_change_role_camp_or_name_of_own_account()
    {
        $s = CampScenario::create();
        $before = $s->participant->only(['role_id', 'camp_id', 'username', 'slug', 'demo']);

        $this->actingAs($s->participant)
            ->patch(route('home.update', $s->participant->id), [
                'email' => $s->participant->email,
                'role_id' => Role::Administrator->value,
                'camp_id' => 1,
                'username' => 'Anderer Name',
                'slug' => 'anderer-slug',
                'demo' => true,
            ]);

        $this->assertSame($before, $s->participant->fresh()->only(array_keys($before)));
    }

    public function test_email_must_be_unique()
    {
        $s = CampScenario::create();
        $email = $s->participant->email;

        $this->actingAs($s->participant)
            ->patch(route('home.update', $s->participant->id), ['email' => $s->gruppenleiter->email])
            ->assertSessionHasErrors('email');

        $this->assertSame($email, $s->participant->fresh()->email);
    }
}
