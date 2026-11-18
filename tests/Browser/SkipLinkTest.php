<?php

namespace Tests\Browser;

use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * «Zum Inhalt springen» erscheint beim ersten Tab und führt in den Inhalt. Font Awesomes .sr-only setzt das alte
 * clip: rect(0,0,0,0), das Tailwinds not-sr-only nicht zurücksetzt; ohne focus:[clip:auto] blieb der Link unsichtbar.
 */
class SkipLinkTest extends DuskTestCase
{
    public function test_skip_link_is_visible_on_first_tab_and_jumps_to_the_content(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->resize(1000, 700)->loginAs($s->kursleiter)->visit('/home');
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::TAB);

            $browser->assertScript('document.activeElement.textContent', 'Zum Inhalt springen')
                ->assertScript('getComputedStyle(document.activeElement).clip', 'auto')
                ->assertScript('document.activeElement.getBoundingClientRect().width > 50', true);

            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);
            $browser->waitUntil("document.activeElement.id === 'inhalt'");
        });
    }
}
