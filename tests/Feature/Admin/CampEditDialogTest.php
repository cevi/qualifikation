<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Dialog «Kurs bearbeiten» der Kursübersicht (resources/js/pages/camps-index.js): die edit-Adresse liefert JSON
 * für den Dialog oder die Übersicht mit offenem Dialog. Als Kursleitung wählbar sind nur Personen mit Rolle
 * Kursleitung in diesem Kurs.
 */
class CampEditDialogTest extends TestCase
{
    public function test_edit_returns_dialog_data_with_only_campleaders_of_this_camp(): void
    {
        $s = CampScenario::create();
        $second = User::factory()->create(['username' => 'Zweite Kursleitung']);
        CampUser::create(['camp_id' => $s->camp->id, 'user_id' => $second->id, 'role_id' => Role::Kursleiter]);
        // Kursleitung eines anderen Kurses
        $other = CampScenario::create();

        $data = $this->actingAs($s->kursleiter)->getJson(route('admin.camps.edit', $s->camp))->assertOk()->json();

        $leaderIds = collect($data['leaders'])->pluck('id')->all();
        $this->assertEqualsCanonicalizing([$s->kursleiter->id, $second->id], $leaderIds);
        $this->assertNotContains($s->gruppenleiter->id, $leaderIds);
        $this->assertNotContains($other->kursleiter->id, $leaderIds);
        $this->assertNotContains($s->admin->id, $leaderIds);
        $this->assertSame($s->camp->name, $data['name']);
        $this->assertSame(route('admin.camps.update', $s->camp), $data['update_url']);
        $this->assertSame(route('admin.camps.destroy', $s->camp), $data['destroy_url']);
    }

    public function test_edit_page_shows_overview_with_open_dialog(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->get(route('admin.camps.edit', $s->camp))
            ->assertOk()
            ->assertSee('id="camp-edit-dialog"', false)
            ->assertSee('data-camp=', false)
            ->assertSee('Kurs abschliessen / löschen');
    }

    public function test_overview_has_no_open_dialog(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->get(route('admin.camps.index'))
            ->assertOk()
            ->assertDontSee('data-camp=', false);
    }

    public function test_table_offers_finishing_only_open_camps(): void
    {
        $s = CampScenario::create();
        $finished = Camp::factory()->finished()->create();

        $rows = collect($this->actingAs($s->admin)->getJson(route('camps.table'))->json('data'))->keyBy('id');

        $finish = collect($rows[$s->camp->id]['actions'])->firstWhere('label', 'Kurs abschliessen');
        $this->assertSame(route('admin.camps.destroy', $s->camp), $finish['url']);
        $this->assertSame('DELETE', $finish['method']);
        $this->assertNotEmpty($finish['confirm']);
        $this->assertNull(collect($rows[$finished->id]['actions'])->firstWhere('label', 'Kurs abschliessen'));
    }

    /** Beim Abschliessen kommen alle Personen ins Global-Camp; es selbst lässt sich nie abschliessen und hat keine Knöpfe. */
    public function test_global_camp_cannot_be_finished(): void
    {
        $s = CampScenario::create();
        $global = Camp::where('global_camp', true)->first() ?? Camp::factory()->create(['global_camp' => true]);
        $this->actingAs($s->admin);

        $row = collect($this->getJson(route('camps.table'))->json('data'))->firstWhere('id', $global->id);
        // Auch kein Export und kein Druck
        $this->assertSame([], $row['actions']);
        $this->assertFalse($this->getJson(route('admin.camps.edit', $global))->json('can_finish'));

        $this->delete(route('admin.camps.destroy', $global))->assertForbidden();
        $this->assertFalse($global->fresh()->finish);
    }

    public function test_campleader_of_this_camp_can_be_chosen(): void
    {
        $s = CampScenario::create();
        $second = User::factory()->create();
        CampUser::create(['camp_id' => $s->camp->id, 'user_id' => $second->id, 'role_id' => Role::Kursleiter]);

        $this->actingAs($s->kursleiter)
            ->patch(route('admin.camps.update', $s->camp), ['name' => $s->camp->name, 'user_id' => $second->id])
            ->assertRedirect('/admin/camps');

        $this->assertSame($second->id, $s->camp->fresh()->user_id);
    }

    public function test_person_without_campleader_role_cannot_be_chosen(): void
    {
        $s = CampScenario::create();
        $other = CampScenario::create();

        foreach ([$s->gruppenleiter, $s->participant, $other->kursleiter] as $user) {
            $this->actingAs($s->kursleiter)
                ->patch(route('admin.camps.update', $s->camp), ['name' => 'Geändert', 'user_id' => $user->id])
                ->assertRedirect(route('admin.camps.edit', $s->camp))
                ->assertSessionHasErrors('user_id');
        }

        $camp = $s->camp->fresh();
        $this->assertSame($s->kursleiter->id, $camp->user_id);
        $this->assertNotSame('Geändert', $camp->name);
    }
}
