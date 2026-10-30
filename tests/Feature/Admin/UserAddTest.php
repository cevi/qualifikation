<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\CampUser;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * "Person hinzufügen" (Personensuche auf "Person erstellen / suchen"): eine bestehende Person in den eigenen Kurs.
 * Im Browser: Browser/UserSearchTest.
 */
class UserAddTest extends TestCase
{
    public function test_campleader_adds_an_existing_person_to_the_own_camp(): void
    {
        $s = CampScenario::create();
        $other = CampScenario::create();
        $person = $other->participant;

        $this->actingAs($s->kursleiter)->post(route('users.add'), ['user_id' => $person->id])
            ->assertRedirect('/admin/users/create');

        $this->assertSame($s->camp->id, $person->fresh()->camp_id);
        $this->assertTrue(CampUser::where('camp_id', $s->camp->id)->where('user_id', $person->id)->where('role_id', Role::Teilnehmer)->exists());
        // Die Leitenden aus dem anderen Kurs kommen nicht mit.
        $this->assertTrue(CampUser::where('camp_id', $s->camp->id)->where('user_id', $person->id)->firstOrFail()->leaders->isEmpty());
    }

    public function test_adding_without_choosing_a_person_shows_an_error(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->from('/admin/users/create')->post(route('users.add'), [])
            ->assertRedirect('/admin/users/create')
            ->assertSessionHasErrors('user_id');
    }
}
