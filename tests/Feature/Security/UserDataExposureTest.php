<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\DemoCampSeeder;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * JSON-Endpunkte, die User ausliefern: die Personensuche beim Hinzufügen und die Personenliste (Tabelle).
 */
class UserDataExposureTest extends TestCase
{
    public function test_participant_cannot_search_users()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)
            ->get(route('searchajaxuser', ['term' => 'example']))
            ->assertRedirect('/home');
    }

    public function test_public_demo_login_cannot_search_users()
    {
        $this->seed(DemoCampSeeder::class);
        User::factory()->create(['email' => 'privat@example.com']);
        $this->get('/loginTN');

        $this->get(route('searchajaxuser', ['term' => 'privat']))
            ->assertRedirect('/home');
    }

    public function test_user_search_returns_only_fields_needed_for_autocomplete()
    {
        $s = CampScenario::create();
        $outsider = User::factory()->create(['username' => 'Zora Aussen', 'email' => 'zora@example.com']);

        $this->actingAs($s->kursleiter)
            ->get(route('searchajaxuser', ['term' => 'Zora']))
            ->assertOk()
            ->assertExactJson([['id' => $outsider->id, 'username' => 'Zora Aussen', 'email' => 'zora@example.com']]);
    }

    public function test_user_search_excludes_admins_and_members_of_own_camp()
    {
        $s = CampScenario::create();

        $ids = collect($this->actingAs($s->kursleiter)
            ->get(route('searchajaxuser', ['term' => '@']))
            ->json())->pluck('id');

        $this->assertNotContains($s->admin->id, $ids);
        $this->assertNotContains($s->participant->id, $ids);
    }

    public function test_user_list_does_not_expose_password_hashes()
    {
        $s = CampScenario::create();

        $response = $this->actingAs($s->kursleiter)
            ->getJson(route('users.table'))
            ->assertOk();

        $this->assertStringNotContainsString('"password"', $response->getContent());
        $this->assertStringNotContainsString('$2y$', $response->getContent());
    }

    public function test_serialized_user_hides_secrets()
    {
        $user = User::factory()->create();

        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->assertArrayNotHasKey('remember_token', $user->toArray());
        $this->assertArrayNotHasKey('last_login_ip', $user->toArray());
    }
}
