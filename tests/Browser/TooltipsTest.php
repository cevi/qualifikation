<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Erklärungen auf der Quali (resources/js/pages/tooltips.js, tippy.js aus npm statt vom CDN): Antippen einer Antwort
 * zeigt ihre Bedeutung und wählt sie an. Logik: tests/js/tooltips.test.js. Den doppelten Klick über das Label
 * (Label und Radio-Knopf) bildet happy-dom nicht nach, darum nur hier geprüft.
 */
class TooltipsTest extends DuskTestCase
{
    public function test_tapping_an_answer_shows_its_meaning(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->participant)->visit(route('survey.survey', $s->survey))
                ->click('[data-accordion-target="#accordion-flush-body-1"]')
                ->waitFor('#accordion-flush-body-1 [data-tooltip="In gutem Masse"]')
                ->click('#accordion-flush-body-1 [data-tooltip="In gutem Masse"]')
                ->waitFor('.tippy-box')
                ->assertSeeIn('.tippy-box', 'In gutem Masse')
                // Der Klick trifft die Zahl, deren Label auf den Radio-Knopf zeigt: Antwort gewählt, Tooltip bleibt offen
                ->assertScript("document.querySelector('#accordion-flush-body-1 [data-tooltip=\"In gutem Masse\"] input').checked", true)
                ->pause(300)
                ->assertVisible('.tippy-box');
            $this->assertStringNotContainsString('unpkg.com', $browser->driver->getPageSource());
        });
    }
}
