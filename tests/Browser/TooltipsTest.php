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
                ->waitFor('#kapitel-1 [data-tooltip="In gutem Masse"]')
                ->click('#kapitel-1 [data-tooltip="In gutem Masse"] label')
                ->waitFor('.tippy-box')
                ->assertSeeIn('.tippy-box', 'In gutem Masse')
                // Der Klick trifft das Label des Radio-Knopfs: Antwort gewählt, Tooltip bleibt offen
                ->assertScript("document.querySelector('#kapitel-1 [data-tooltip=\"In gutem Masse\"] input').checked", true)
                ->pause(300)
                ->assertVisible('.tippy-box');

            $this->assertStringNotContainsString('unpkg.com', $browser->driver->getPageSource());
        });
    }
}
