<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    public function test_admin_can_create_group_without_cevi_db_id()
    {
        $this->actingAs(User::findOrFail(1))
            ->post(route('groups.store'), ['name' => 'Cevi Testdorf', 'shortname' => 'TD'])
            ->assertRedirect('admin/groups');

        $this->assertDatabaseHas('groups', ['name' => 'Cevi Testdorf', 'foreign_id' => null]);
    }

    public function test_group_in_use_is_not_deleted_and_shows_why()
    {
        // CampScenario: Kurs und vier Personen gehören zur Gruppe
        $s = CampScenario::create();

        $this->actingAs($s->admin)
            ->from(route('groups.edit', $s->group))
            ->delete(route('groups.destroy', $s->group))
            ->assertRedirect(route('groups.edit', $s->group))
            ->assertSessionHasErrors(['group' => 'Die Gruppe wird noch von 4 Person(en) und 1 Kurs(en) verwendet und kann nicht gelöscht werden.']);

        $this->assertModelExists($s->group);
        // Die Bearbeiten-Seite zeigt die Meldung über dem Löschen-Knopf
        $this->actingAs($s->admin)->get(route('groups.edit', $s->group))->assertSee('verwendet und kann nicht gelöscht werden.');
    }

    public function test_unused_group_is_deleted()
    {
        $s = CampScenario::create();
        $group = Group::factory()->create();

        $this->actingAs($s->admin)
            ->delete(route('groups.destroy', $group))
            ->assertRedirect('/admin/groups');

        $this->assertModelMissing($group);
    }
}
