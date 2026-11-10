<?php

namespace Tests\Browser;

use App\Models\Survey;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * «Erste Schritte» auf der Übersicht: Ausblenden und Einblenden ohne JS, «Qualifikationen erstellen» aus der Karte mit
 * Rückfrage und zurück auf die Übersicht.
 */
class CampChecklistTest extends DuskTestCase
{
    public function test_campleader_hides_and_shows_the_card(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit('/home')
                ->waitFor('#erste-schritte')
                ->assertVisible('[data-step="leaders"][aria-current="step"]');

            $this->clickCentered($browser, '#erste-schritte button[type="submit"]');
            $browser->waitForText('Einblenden')
                ->assertMissing('[data-step="leaders"]')
                ->assertSee('3 von 8 erledigt');

            $this->clickCentered($browser, '#erste-schritte button[type="submit"]');
            $browser->waitFor('[data-step="leaders"]')->assertSee('Ausblenden');
            $this->assertSame([], $this->consoleErrors($browser));
        });
    }

    public function test_surveys_are_created_from_the_card_after_confirming(): void
    {
        $s = CampScenario::create();
        $newcomer = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();
        $s->campUser($s->unassignedParticipant)->leaders()->attach($s->gruppenleiter->id);

        $this->browse(function (Browser $browser) use ($s, $newcomer) {
            $browser->loginAs($s->kursleiter)->visit('/home')
                ->waitFor('[data-step="surveys"][aria-current="step"]');

            $this->clickCentered($browser, '[data-step="surveys"] button[data-confirm]');
            $browser->waitFor('dialog[data-confirm-dialog][open]')->click('[data-dialog-confirm]')
                ->waitForLocation('/home')
                ->waitFor('[data-step="posts"][aria-current="step"]');

            $this->assertTrue(Survey::whereHas('campuser', fn ($query) => $query->where('user_id', $newcomer->id))->exists());
        });
    }
}
