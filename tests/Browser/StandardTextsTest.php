<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Textbausteine im Fenster "Neue Rückmeldung" auf der Profilseite. Zwei Skripte hören auf Escape
 * (Rückmeldung und Textbausteine); Escape schliesst nur die Auswahl. Die Logik prüft tests/js/standard-texts.test.js.
 */
class StandardTextsTest extends DuskTestCase
{
    public function test_double_slash_inserts_standard_text_into_new_feedback(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->gruppenleiter)->visit(route('home.profile', $s->participant));
            $this->clickCentered($browser, '#new-feedback-btn');
            $browser->waitFor('#feedback-modal textarea[name="comment"]');

            $browser->type('#comment', 'Fazit: //');
            $this->waitForPicker($browser, true);
            $browser->assertSeeIn('#optionTable', $s->standardText->title)
                ->keys('#comment', '{enter}');
            $this->waitForPicker($browser, false);
            $browser->assertInputValue('#comment', 'Fazit: '.$s->standardText->content);

            $browser->keys('#comment', '/', '/');
            $this->waitForPicker($browser, true);
            $browser->keys('#comment', '{escape}');
            $this->waitForPicker($browser, false);
            $browser->assertVisible('#feedback-modal')
                ->assertInputValue('#comment', 'Fazit: '.$s->standardText->content);
        });
    }

    public function test_double_slash_in_the_middle_inserts_at_the_cursor(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->gruppenleiter)->visit(route('home.profile', $s->participant));
            $this->clickCentered($browser, '#new-feedback-btn');
            $browser->waitFor('#feedback-modal textarea[name="comment"]')
                ->type('#comment', 'Hallo Welt')
                ->script('const field = document.getElementById("comment"); field.focus(); field.setSelectionRange(6, 6);');

            $browser->keys('#comment', '/', '/');
            $this->waitForPicker($browser, true);
            $browser->keys('#comment', '{enter}');
            $this->waitForPicker($browser, false);
            $browser->assertInputValue('#comment', 'Hallo '.$s->standardText->content.'Welt');
        });
    }

    private function waitForPicker(Browser $browser, bool $open): void
    {
        $browser->waitUntil('(document.getElementById("default-modal").style.display === "flex") === '.json_encode($open));
    }
}
