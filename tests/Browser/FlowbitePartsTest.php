<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * app.js lädt nur noch fünf Flowbite-Komponenten (resources/js/libs/flowbite-parts.js). Jede wird einmal im Browser
 * bedient; fehlt eine beim Import, tut der Knopf nichts.
 */
class FlowbitePartsTest extends DuskTestCase
{
    public function test_dropdown_drawer_and_dismiss_work_on_the_desktop(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->resize(1440, 1000)->loginAs($s->kursleiter)->visit('/home')
                ->click('#user-menu-button')
                ->waitFor('#dropdown');

            // Hilfe rechts: der Drawer schiebt sich herein (Flowbite entfernt translate-x-full)
            $browser->visit(route('home.profile', $s->participant->slug));
            $this->clickCentered($browser, '[data-drawer-show="drawer-right-example"]');
            $browser->waitUntil("!document.getElementById('drawer-right-example').classList.contains('translate-x-full')");

            // Meldung nach dem Passwortwechsel lässt sich schliessen
            $browser->visit(route('home.user', $s->kursleiter->slug))
                ->type('password', 'neues-passwort-123')
                ->type('password_confirmation', 'neues-passwort-123')
                ->press('Speichern')
                ->waitFor('#alert-message')
                ->click('#alert-message [data-dismiss-target]')
                ->waitUntilMissing('#alert-message');

            $this->assertSame([], $this->consoleErrors($browser));
        });
    }

    public function test_accordion_and_collapse_work(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->resize(1440, 1000)->loginAs($s->participant)->visit(route('survey.survey', $s->survey->slug));
            $this->clickCentered($browser, '[data-accordion-target]');
            $browser->waitUntil("!document.querySelector(document.querySelector('[data-accordion-target]').dataset.accordionTarget).classList.contains('hidden')");

            // Hamburger auf dem Handy
            $browser->resize(390, 844)->visit('/home')
                ->click('[data-collapse-toggle="mobile-menu-2"]')
                ->waitUntil("!document.getElementById('mobile-menu-2').classList.contains('hidden')");

            $this->assertSame([], $this->consoleErrors($browser));
        });
    }
}
