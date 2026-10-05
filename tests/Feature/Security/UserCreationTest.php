<?php

namespace Tests\Feature\Security;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Personen neu anlegen (POST admin/users). Kursleitende vergeben dabei das Startpasswort.
 */
class UserCreationTest extends TestCase
{
    public function test_kursleiter_creates_person_with_initial_password()
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->post(route('admin.users.store'), $this->formData())
            ->assertRedirect('/admin/users/create');

        $person = User::where('email', 'neu@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('startpasswort', $person->password));
        $this->assertSame($s->camp->id, $person->camp_id);
        $this->assertSame(Role::Teilnehmer, $person->role_id);
    }

    public function test_kursleiter_cannot_create_admin()
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->post(route('admin.users.store'), $this->formData(['role_id' => Role::Administrator->value]));

        $this->assertFalse(User::where('email', 'neu@example.com')->first()?->isAdmin() ?? false);
    }

    public function test_kursleiter_cannot_create_person_in_foreign_camp()
    {
        $a = CampScenario::create();
        $b = CampScenario::create();

        $this->actingAs($a->kursleiter)
            ->post(route('admin.users.store'), $this->formData(['camp_id' => $b->camp->id]));

        $this->assertSame($a->camp->id, User::where('email', 'neu@example.com')->firstOrFail()->camp_id);
    }

    public function test_admin_may_choose_administrator_role()
    {
        $s = CampScenario::create();

        $this->actingAs($s->admin)
            ->post(route('admin.users.store'), $this->formData(['role_id' => Role::Administrator->value]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'neu@example.com']);
    }

    private function formData(array $overrides = []): array
    {
        return $overrides + [
            'username' => 'Neue Person',
            'email' => 'neu@example.com',
            'password' => 'startpasswort',
            'role_id' => Role::Teilnehmer->value,
        ];
    }
}
