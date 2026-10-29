<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Menü unten auf dem Handy: "Neu" öffnet direkt das Formular für eine Rückmeldung, "Mehr" das Menü oben; auf dem
 * Desktop ist das Menü unten ausgeblendet. Punkte je Rolle: Feature/BottomNavigationTest. Dazu der Service Worker
 * der PWA: Offline-Seite gespeichert, besuchte Seiten nicht (Feature/PwaTest).
 */
class MobileNavigationTest extends DuskTestCase
{
    public function test_new_opens_the_feedback_form_and_more_opens_the_menu(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->gruppenleiter)->resize(390, 800)->visit('/home')
                ->waitFor('#bottom-navigation')
                ->assertMissing('#mobile-menu-2:not(.hidden)')
                ->click('#bottom-navigation [data-bottom-nav-more]')
                ->waitFor('#mobile-menu-2:not(.hidden)')
                ->click('#bottom-navigation a[aria-label="Neue Rückmeldung"]')
                ->waitForLocation('/posts')
                ->waitFor('#feedback-modal:not(.hidden)')
                ->assertFocused('#feedback-modal textarea[name="comment"]');
        });
    }

    public function test_service_worker_keeps_offline_page_but_no_pages(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->gruppenleiter)->visit('/home')
                ->script('navigator.serviceWorker.ready.then(() => { window.swReady = true; })');
            $browser->waitUntil('window.swReady === true')
                // Zweiter Besuch: jetzt läuft die Seite über den Service Worker
                ->visit('/home')
                ->waitUntil('navigator.serviceWorker.controller !== null');

            $browser->script("Promise.all([caches.match('/offline.html'), caches.match('/home')]).then(([offline, page]) => { window.cached = [!! offline, !! page]; })");
            $browser->waitUntil('Array.isArray(window.cached)');
            $this->assertSame([true, false], $browser->script('return window.cached')[0]);
        });
    }

    public function test_bottom_navigation_is_hidden_on_desktop(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->gruppenleiter)->resize(1280, 800)->visit('/home')
                ->assertMissing('#bottom-navigation');
        });
    }
}
