<?php

namespace Tests\Browser;

use App\Enums\SurveyStatus;
use App\Models\Camp;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Admin-Tabellen (components/DataTable.vue) mit den echten Endpunkten: Zeilen erscheinen, Suche und Sortierung
 * wirken, Kurs- und Quali-Liste filtern (offen/abgeschlossen bzw. nach Status), die Personenliste merkt sich die Sortierung in der
 * Adresse. Logik: tests/js/data-table.test.js.
 */
class AdminTablesTest extends DuskTestCase
{
    public function test_person_list_loads_searches_and_keeps_sorting_in_address(): void
    {
        $s = CampScenario::create();
        $s->participant->update(['username' => 'Zora Zuletzt']);
        $s->unassignedParticipant->update(['username' => 'Anna Anfang']);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.users.index'))
                ->waitForText('Zora Zuletzt')
                ->assertSee('Anna Anfang');

            $browser->type('input[type="search"]', 'zora')
                ->waitUntilMissingText('Anna Anfang')
                ->assertSee('1–1 von 1')
                ->clear('input[type="search"]')
                ->type('input[type="search"]', ' ')
                ->waitForText('Anna Anfang');

            // Klick sortiert nur nach Name, der nächste Klick dreht die Richtung.
            $browser->click('thead th:first-child button')
                ->waitForLocation('/admin/users')
                ->assertQueryStringHas('order', 'name:asc')
                ->click('thead th:first-child button')
                ->assertQueryStringHas('order', 'name:desc');
            $this->assertSame(['Zora Zuletzt', 'Anna Anfang'], $this->namesInOrder($browser, ['Anna Anfang', 'Zora Zuletzt']));

            $browser->refresh()->waitForText('Zora Zuletzt');
            $this->assertSame(['Zora Zuletzt', 'Anna Anfang'], $this->namesInOrder($browser, ['Anna Anfang', 'Zora Zuletzt']));

            $browser->back()->waitForText('Zora Zuletzt')->assertQueryStringHas('order', 'name:asc');
            $this->assertSame(['Anna Anfang', 'Zora Zuletzt'], $this->namesInOrder($browser, ['Anna Anfang', 'Zora Zuletzt']));

            $browser->back()->waitForText('Zora Zuletzt')->assertQueryStringMissing('order');
        });
    }

    public function test_survey_and_camp_lists_show_rows(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('surveys.index'))
                ->waitForText($s->participant->username)
                ->assertPresent('tbody [data-status-bar]')
                ->assertSeeLink('Zur Qualifikation');

            $browser->loginAs($s->admin)->visit(route('admin.camps.index'))
                ->waitForText($s->camp->name)
                ->assertSeeLink('Rückmeldungen exportieren');
        });
    }

    /** Kursname öffnet «Kurs bearbeiten» als Dialog; zur Wahl steht als Kursleitung nur die Kursleitung des Kurses. */
    public function test_camp_name_opens_edit_dialog(): void
    {
        $s = CampScenario::create();
        // Pflichtfeld «Organisierende Gruppe» bietet nur Kursgruppen an
        $s->group->update(['campgroup' => true]);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.camps.index'))
                ->waitForText($s->camp->name)
                ->click('a[data-open-dialog]')
                ->waitFor('#camp-edit-dialog[open]')
                ->assertPathIs('/admin/camps')
                ->assertInputValue('#edit_name', $s->camp->name)
                ->assertSelected('#edit_user_id', (string) $s->kursleiter->id)
                ->assertSelectMissingOption('#edit_user_id', (string) $s->gruppenleiter->id)
                ->assertVisible('#camp-edit-dialog form#DeleteForm .confirm')
                ->type('#edit_name', 'Umbenannt')
                ->press('Änderungen speichern')
                ->waitForText('Umbenannt');

            $browser->press('Neuer Kurs')
                ->waitFor('#camp-create-dialog[open]')
                ->assertMissing('#camp-create-dialog [name="user_id"]')
                ->assertMissing('#camp-create-dialog form#DeleteForm');
        });

        $this->assertSame('Umbenannt', $s->camp->fresh()->name);
    }

    public function test_quali_list_filters_by_phase(): void
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('surveys.index'))
                ->waitForText($s->participant->username)
                ->assertSee($s->unassignedParticipant->username)
                ->press('Zum Abschliessen')
                ->waitUntilMissingText($s->unassignedParticipant->username)
                ->assertSee($s->participant->username)
                ->press('Offen')
                ->waitForText($s->unassignedParticipant->username)
                ->assertDontSee($s->participant->username);
        });
    }

    public function test_camp_list_filters_open_and_finished_camps(): void
    {
        $s = CampScenario::create();
        $finished = Camp::factory()->finished()->create(['name' => 'Abgeschlossener Kurs']);

        $this->browse(function (Browser $browser) use ($s, $finished) {
            $browser->loginAs($s->admin)->visit(route('admin.camps.index'))
                ->waitForText($s->camp->name)
                ->assertSee($finished->name)
                ->press('Offene')
                ->waitUntilMissingText($finished->name)
                ->assertSee($s->camp->name)
                ->press('Abgeschlossene')
                ->waitForText($finished->name)
                ->assertDontSee($s->camp->name)
                ->press('Alle')
                ->waitForText($s->camp->name)
                ->assertSee($finished->name);
        });
    }

    /** Reihenfolge, in der die Namen in der ersten Spalte stehen. */
    private function namesInOrder(Browser $browser, array $names): array
    {
        $column = $browser->script("return [...document.querySelectorAll('tbody tr td:first-child')].map((td) => td.textContent.trim())")[0];

        return array_values(array_filter($column, fn ($name) => in_array($name, $names, true)));
    }
}
