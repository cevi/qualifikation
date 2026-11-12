<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Models\Answer;
use App\Models\Competence;
use App\Models\SurveyQuestion;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Aufbau von Quali (ausfüllen) und Vergleich: Kapitel offen mit Sprungleiste, Kernkompetenz mit Beschreibung zum
 * Antippen, Speicherstand, Hinweis auf die Selbsteinschätzung für Leitende, eine Spalte pro Einschätzung und
 * markierte Abweichungen im Vergleich.
 */
class SurveyPagesTest extends TestCase
{
    private CampScenario $s;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
    }

    private function answer(string $name): int
    {
        return Answer::where('name', $name)->value('id');
    }

    /** @return array{0: SurveyQuestion, 1: SurveyQuestion} zwei Fragen der Quali der TN-Person */
    private function twoQuestions(): array
    {
        $questions = $this->s->survey->questions()->with('question')->get();

        return [$questions[0], $questions[1]];
    }

    public function test_participant_fills_in_chapters_with_core_competence_and_save_status(): void
    {
        $competence = Competence::where('camp_type_id', $this->s->camp->camp_type_id)->with('question')->firstOrFail();
        $question = $this->s->survey->questions()->where('question_id', $competence->question_id)->firstOrFail();

        $html = $this->actingAs($this->s->participant)->get(route('survey.survey', $this->s->survey))->assertOk()
            ->assertSee('href="#kapitel-1"', false)
            ->assertSee('<details open id="kapitel-1"', false)
            ->assertSee('data-tooltip="'.e($competence->name).'"', false)
            ->assertSee('data-save-status', false)
            ->assertDontSee('TN selbst:')
            ->assertDontSee('id="bemerkung"', false)
            ->getContent();

        // Radio-IDs wie bisher (Frage.Runde.NummerAntwort), radar.js trägt angetippte Antworten damit ein
        $this->assertStringContainsString('id="'.$competence->question_id.'.1.'.$competence->question->number.$this->answer('+').'"', $html);
        $this->assertStringContainsString('name="comments['.$question->id.']"', $html);
        // Abschliessen oben in der Leiste und am Ende, beide mit Rückfrage
        $this->assertSame(2, substr_count($html, 'name="action" value="close"'));
        $this->assertSame(2, substr_count($html, 'data-confirm="1. Selbsteinschätzung abschliessen?"'));
    }

    public function test_leader_sees_self_assessment_as_hint_and_writes_remark(): void
    {
        [$question] = $this->twoQuestions();
        SurveyQuestion::whereKey($question->id)->update(['answer_first_id' => $this->answer('+'), 'answer_second_id' => $this->answer('++')]);

        $html = $this->actingAs($this->s->gruppenleiter)->get(route('survey.survey', $this->s->survey))->assertOk()
            ->assertSee('Einschätzung der Leitenden')
            ->assertSee('TN selbst:')
            ->assertSee('id="bemerkung"', false)
            ->assertSee(route('survey.compare', $this->s->survey->slug))
            ->assertDontSee('name="action" value="close"', false)
            ->getContent();

        $this->assertStringContainsString('data-tooltip="In gutem Masse"', $html);
    }

    public function test_participant_compare_shows_only_own_columns(): void
    {
        $this->actingAs($this->s->participant)->get(route('survey.compare', $this->s->survey))->assertOk()
            ->assertSee('1. Selbsteinschätzung')
            ->assertDontSee('Spalte «Leitende»')
            ->assertDontSee('Nur Abweichungen')
            ->assertDontSee('Druckansicht')
            ->assertDontSee('Bemerkung');
    }

    public function test_leader_compare_marks_differences_of_two_steps(): void
    {
        $this->s->camp->update(['secondsurveyopen' => true]);
        [$differs, $close] = $this->twoQuestions();
        SurveyQuestion::whereKey($differs->id)->update(['answer_second_id' => $this->answer('++'), 'answer_leader_id' => $this->answer('0'), 'comment_leader' => "Zeile 1\nZeile 2"]);
        SurveyQuestion::whereKey($close->id)->update(['answer_second_id' => $this->answer('+'), 'answer_leader_id' => $this->answer('++')]);

        $html = $this->actingAs($this->s->gruppenleiter)->get(route('survey.compare', $this->s->survey))->assertOk()
            ->assertSee('Spalte «Leitende»')
            ->assertSee('Nur Abweichungen (1)')
            ->assertSee('2. Selbsteinschätzung')
            ->assertSee(route('survey.downloadPDF', $this->s->survey))
            ->assertSee('Zeile 1<br />', false)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'data-differs'));
    }

    public function test_feedback_card_only_with_feedback_on_the_quali(): void
    {
        $heading = 'md:text-lg dark:text-white">Rückmeldungen</h2>';
        $this->s->post->update(['show_on_survey' => false]);

        foreach ([route('survey.survey', $this->s->survey), route('survey.compare', $this->s->survey)] as $url) {
            $this->actingAs($this->s->gruppenleiter)->get($url)->assertOk()->assertDontSee($heading, false);
        }

        $this->s->post->update(['show_on_survey' => true, 'comment' => 'Ruhig und klar']);

        foreach ([route('survey.survey', $this->s->survey), route('survey.compare', $this->s->survey)] as $url) {
            $this->actingAs($this->s->gruppenleiter)->get($url)->assertOk()
                ->assertSee($heading, false)
                ->assertSee('Ruhig und klar');
        }
    }

    public function test_print_view_has_the_compare_layout_with_all_assessments(): void
    {
        [$differs] = $this->twoQuestions();
        SurveyQuestion::whereKey($differs->id)->update(['answer_second_id' => $this->answer('++'), 'answer_leader_id' => $this->answer('-')]);

        $this->actingAs($this->s->gruppenleiter)->get(route('survey.downloadPDF', $this->s->survey))->assertOk()
            ->assertSee($this->s->participant->username)
            ->assertSee($this->s->campUser($this->s->participant)->leaderNames())
            ->assertSeeInOrder(['1. Selbsteinschätzung', '2. Selbsteinschätzung', 'Leitende'])
            ->assertSee('Abweichung')
            ->assertSee('[print-color-adjust:exact]', false)
            ->assertDontSee('site.webmanifest', false);
    }

    public function test_second_self_assessment_shows_own_first_answers(): void
    {
        [$question] = $this->twoQuestions();
        SurveyQuestion::whereKey($question->id)->update(['answer_first_id' => $this->answer('++'), 'answer_leader_id' => $this->answer('-')]);
        $hint = '1. Selbsteinschätzung:';

        $this->actingAs($this->s->participant)->get(route('survey.survey', $this->s->survey))->assertOk()->assertDontSee($hint);

        $this->s->camp->update(['secondsurveyopen' => true]);
        $this->s->survey->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
        $html = $this->actingAs($this->s->participant->fresh())->get(route('survey.survey', $this->s->survey))->assertOk()
            ->assertSee($hint)
            ->assertDontSee('TN selbst:')
            ->getContent();

        $this->assertStringContainsString('data-tooltip="Ausgezeichnet. Diese Kompetenz ist eine Stärke des Teilnehmenden."', $html);
        $this->assertStringNotContainsString('data-tooltip="Nicht in ausreichendem Masse. Es sind Defizite vorhanden"', $this->hintsOf($html), 'Leitenden-Bewertung bleibt verborgen');
    }

    /** Nur die Hinweise unter den Knöpfen, ohne Legende und Radio-Knöpfe. */
    private function hintsOf(string $html): string
    {
        preg_match_all('/1\. Selbsteinschätzung:.*?<\/p>/s', $html, $matches);

        return implode('', $matches[0]);
    }
}
