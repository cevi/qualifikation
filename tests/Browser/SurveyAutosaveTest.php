<?php

namespace Tests\Browser;

use App\Models\Answer;
use App\Models\SurveyQuestion;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Quali ausfüllen ohne Speichern-Knopf: Antwort und Kommentar sind nach "Gespeichert ✓" auf dem Server
 * und nach dem Neuladen noch da (der Kommentar ist dann schon aufgeklappt). Die Logik selbst prüft tests/js/survey-autosave.test.js.
 */
class SurveyAutosaveTest extends DuskTestCase
{
    public function test_leader_answer_and_comment_are_saved_without_pressing_save(): void
    {
        $s = CampScenario::create();
        $question = $s->survey->questions()->firstOrFail();
        $plus = Answer::where('name', '+')->value('id');
        $radio = 'input[name="answers['.$question->id.']"][value="'.$plus.'"]';
        $comment = 'textarea[name="comments['.$question->id.']"]';

        $this->browse(function (Browser $browser) use ($s, $radio, $comment) {
            $browser->loginAs($s->gruppenleiter)->visit(route('survey.survey', $s->survey));

            // Der Radio-Knopf ist unsichtbar, angetippt wird sein Label; der Kommentar klappt erst auf Klick auf
            $this->clickCentered($browser, $radio.' + label');
            $this->clickCentered($browser, 'details[data-comment]:has('.$comment.') > summary');
            $browser->type($comment, 'Sicher im Auftritt')
                ->waitForTextIn('button[name="action"][value="save"]', 'Gespeichert ✓')
                ->assertSeeIn('[data-save-status]', 'Gespeichert');

            $browser->refresh()
                ->waitFor($comment)
                ->assertChecked($radio)
                ->assertInputValue($comment, 'Sicher im Auftritt');
        });

        $saved = SurveyQuestion::findOrFail($question->id);
        $this->assertSame($plus, $saved->answer_leader_id);
        $this->assertSame('Sicher im Auftritt', $saved->comment_leader);
    }
}
