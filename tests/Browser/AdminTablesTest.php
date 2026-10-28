<?php

namespace Tests\Browser;

use App\Models\Camp;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Admin-Tabellen (components/DataTable.vue) mit den echten Endpunkten: Zeilen erscheinen, Suche und Sortierung
 * wirken, die Kursliste filtert nach offen/abgeschlossen, die Personenliste merkt sich die Sortierung in der
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
                ->assertPresent('tbody .progressbar li.active')
                ->assertSeeLink('Zur Qualifikationen');

            $browser->loginAs($s->admin)->visit(route('admin.camps.index'))
                ->waitForText($s->camp->name)
                ->assertSeeLink('Export der Rückmeldungen');
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
