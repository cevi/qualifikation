<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Models\Answer;
use App\Models\SurveyQuestion;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Ablauf einer Quali: 1. Selbsteinschätzung, Abschliessen, 2. Selbsteinschätzung, Leiter-Einschätzung, Fertigstellen.
 */
class SurveyFlowTest extends TestCase
{
    /**
     * Darf die TN-Person ihre Selbsteinschätzung ausfüllen? [Kurs-Einstellungen, Status der Quali, erlaubt]
     */
    public static function participantAccess(): array
    {
        return [
            '1. Runde, Status Neu' => [[], SurveyStatus::Neu, true],
            '1. Runde, Status 1. offen' => [[], SurveyStatus::ErsteRundeOffen, true],
            'Status-Steuerung, 1. Runde noch nicht freigegeben' => [['status_control' => true, 'survey_status_id' => SurveyStatus::Neu], SurveyStatus::Neu, false],
            'Status-Steuerung, 1. Runde freigegeben' => [['status_control' => true, 'survey_status_id' => SurveyStatus::ZweiteRundeOffen], SurveyStatus::Neu, true],
            'Selbsteinschätzung abgeschlossen' => [[], SurveyStatus::TnAbgeschlossen, false],
            '2. Runde nicht freigegeben' => [[], SurveyStatus::ZweiteRundeOffen, false],
            '2. Runde freigegeben' => [['secondsurveyopen' => true], SurveyStatus::ZweiteRundeOffen, true],
            'Status-Steuerung, 2. Runde freigegeben' => [['status_control' => true, 'survey_status_id' => SurveyStatus::TnAbgeschlossen, 'secondsurveyopen' => true], SurveyStatus::ZweiteRundeOffen, true],
            'Quali fertig' => [['secondsurveyopen' => true], SurveyStatus::Fertig, false],
        ];
    }

    #[DataProvider('participantAccess')]
    public function test_participant_access_to_own_quali(array $campSettings, SurveyStatus $status, bool $allowed)
    {
        $s = $this->scenarioWith($campSettings, $status);

        $response = $this->actingAs($s->participant->fresh())->get(route('survey.survey', $s->survey));

        $allowed ? $response->assertOk() : $response->assertRedirect('/home');
    }

    #[DataProvider('participantAccess')]
    public function test_participant_can_save_only_when_allowed(array $campSettings, SurveyStatus $status, bool $allowed)
    {
        $s = $this->scenarioWith($campSettings, $status);

        $response = $this->actingAs($s->participant->fresh())
            ->patch(route('survey.update', $s->survey), ['answers' => [$this->question($s)->id => $this->answer('+')]]);

        $allowed ? $response->assertRedirect() : $response->assertForbidden();
    }

    public function test_assigned_leader_fills_in_until_quali_is_finished()
    {
        $s = CampScenario::create();

        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->actingAs($s->gruppenleiter)->get(route('survey.survey', $s->survey))->assertOk();

        $s->survey->update(['survey_status_id' => SurveyStatus::Fertig]);
        $this->actingAs($s->gruppenleiter)->get(route('survey.survey', $s->survey))->assertRedirect('/home');
    }

    public function test_first_round_saves_into_first_answers_and_opens_quali()
    {
        $s = CampScenario::create();
        $question = $this->question($s);

        $this->actingAs($s->participant)->patch(route('survey.update', $s->survey), [
            'answers' => [$question->id => $this->answer('+')],
            'comments' => [$question->id => 'Erste Runde'],
        ]);

        $question->refresh();
        $this->assertSame($this->answer('+'), $question->answer_first_id);
        $this->assertSame('Erste Runde', $question->comment_first);
        $this->assertSame($this->answer('0'), $question->answer_second_id);
        $this->assertSame(SurveyStatus::ErsteRundeOffen, $s->survey->fresh()->survey_status_id);
    }

    public function test_closing_first_round_finishes_self_assessment()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)
            ->patch(route('survey.update', $s->survey), ['action' => 'close'])
            ->assertRedirect('/home');

        $this->assertSame(SurveyStatus::TnAbgeschlossen, $s->survey->fresh()->survey_status_id);
    }

    public function test_closing_first_round_moves_on_to_second_round_if_already_released()
    {
        $s = CampScenario::create();
        $s->camp->update(['secondsurveyopen' => true]);

        $this->actingAs($s->participant->fresh())->patch(route('survey.update', $s->survey), ['action' => 'close']);

        $this->assertSame(SurveyStatus::ZweiteRundeOffen, $s->survey->fresh()->survey_status_id);
    }

    public function test_second_round_saves_into_second_answers_and_closes()
    {
        $s = CampScenario::create();
        $s->camp->update(['secondsurveyopen' => true]);
        $s->survey->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
        $question = $this->question($s);

        $this->actingAs($s->participant->fresh())->patch(route('survey.update', $s->survey), [
            'answers' => [$question->id => $this->answer('++')],
            'comments' => [$question->id => 'Zweite Runde'],
            'action' => 'close',
        ]);

        $question->refresh();
        $this->assertSame($this->answer('0'), $question->answer_first_id);
        $this->assertSame($this->answer('++'), $question->answer_second_id);
        $this->assertSame('Zweite Runde', $question->comment_second);
        $this->assertSame(SurveyStatus::TnAbgeschlossen, $s->survey->fresh()->survey_status_id);
    }

    public function test_leader_saves_leader_assessment_without_changing_status()
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $question = $this->question($s);

        $this->actingAs($s->gruppenleiter)->patch(route('survey.update', $s->survey), [
            'answers' => [$question->id => $this->answer('-')],
            'comments' => [$question->id => 'Sicht der Leitung'],
            'comment' => 'Gesamteindruck',
            'action' => 'close',
        ]);

        $question->refresh();
        $this->assertSame($this->answer('-'), $question->answer_leader_id);
        $this->assertSame('Sicht der Leitung', $question->comment_leader);
        $this->assertSame($this->answer('0'), $question->answer_first_id);
        $survey = $s->survey->fresh();
        $this->assertSame('Gesamteindruck', $survey->comment);
        $this->assertSame(SurveyStatus::TnAbgeschlossen, $survey->survey_status_id);
    }

    public static function leaderAssessesBeforeRelease(): array
    {
        return ['vor Freigabe 2. Runde' => [true], 'während 2. Runde' => [false]];
    }

    /**
     * Rückmeldung: Der Gruppenleiter bewertet, bevor die TN-Person die 2. Selbsteinschätzung gemacht hat,
     * und sie sieht dann seine Bewertung als ihre 2. Einschätzung. Leitung bewertet vor oder nach der Freigabe der 2. Runde.
     */
    #[DataProvider('leaderAssessesBeforeRelease')]
    public function test_leader_assessment_does_not_show_up_as_second_self_assessment(bool $beforeRelease)
    {
        $s = CampScenario::create();
        $question = $this->question($s);

        $this->actingAs($s->participant)->patch(route('survey.update', $s->survey), [
            'answers' => [$question->id => $this->answer('+')],
            'comments' => [$question->id => 'Erste Runde'],
            'action' => 'close',
        ]);
        $leaderAssesses = fn () => $this->actingAs($s->gruppenleiter)->patch(route('survey.update', $s->survey), [
            'answers' => [$question->id => $this->answer('-')],
            'comments' => [$question->id => 'Sicht der Leitung'],
            'comment' => 'Gesamteindruck',
        ])->assertRedirect();
        if ($beforeRelease) {
            $leaderAssesses();
        }
        $this->actingAs($s->kursleiter)->post(route('admin.camps.opensurvey'));
        if (! $beforeRelease) {
            $leaderAssesses();
        }

        $question->refresh();
        $this->assertSame(SurveyStatus::ZweiteRundeOffen, $s->survey->fresh()->survey_status_id);
        $this->assertSame($this->answer('-'), $question->answer_leader_id);
        $this->assertSame($this->answer('0'), $question->answer_second_id);
        $this->assertNotSame('Sicht der Leitung', $question->comment_second);

        // 2. Selbsteinschätzung ausfüllen: vorausgewählt ist "0", nicht die Bewertung der Leitung.
        $response = $this->actingAs($s->participant->fresh())->get(route('survey.survey', $s->survey))->assertOk();
        $this->assertSame([$this->answer('0')], $this->checkedAnswers($response->getContent(), $question));
        $response->assertDontSee('Sicht der Leitung')->assertDontSee('Gesamteindruck');
        $this->assertChartWithoutLeader($response->viewData('datasets'));

        // Vergleich: nur die eigenen beiden Runden.
        $response = $this->actingAs($s->participant->fresh())->get(route('survey.compare', $s->survey))->assertOk();
        $response->assertSee('Erste Runde')->assertDontSee('Sicht der Leitung')->assertDontSee('Gesamteindruck');
        $this->assertChartWithoutLeader($response->viewData('datasets'));
    }

    public function test_releasing_first_round_with_status_control_lets_participants_start()
    {
        $s = CampScenario::create();
        $s->camp->update(['status_control' => true, 'survey_status_id' => SurveyStatus::Neu]);
        $this->actingAs($s->participant->fresh())->get(route('survey.survey', $s->survey))->assertRedirect('/home');

        $this->actingAs($s->kursleiter)->post(route('admin.camps.opensurvey'))->assertRedirect('/admin/surveys');

        $this->actingAs($s->participant->fresh())->get(route('survey.survey', $s->survey))->assertOk();
    }

    public function test_releasing_second_round_reopens_closed_self_assessments_only()
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $s->unassignedSurvey->update(['survey_status_id' => SurveyStatus::ErsteRundeOffen]);

        $this->actingAs($s->kursleiter)->post(route('admin.camps.opensurvey'));

        $this->assertTrue($s->camp->fresh()->secondsurveyopen);
        $this->assertSame(SurveyStatus::ZweiteRundeOffen, $s->survey->fresh()->survey_status_id);
        $this->assertSame(SurveyStatus::ErsteRundeOffen, $s->unassignedSurvey->fresh()->survey_status_id);
        $this->actingAs($s->participant->fresh())->get(route('survey.survey', $s->survey))->assertOk();
    }

    public function test_leader_finishes_only_closed_quali()
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::ErsteRundeOffen]);

        $this->actingAs($s->gruppenleiter)->patch(route('survey.finish', $s->survey->id));
        $this->assertSame(SurveyStatus::ErsteRundeOffen, $s->survey->fresh()->survey_status_id);

        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->actingAs($s->gruppenleiter)->patch(route('survey.finish', $s->survey->id));
        $this->assertSame(SurveyStatus::Fertig, $s->survey->fresh()->survey_status_id);
    }

    public function test_finish_button_only_while_it_can_finish()
    {
        $s = CampScenario::create();
        $compare = fn () => $this->actingAs($s->gruppenleiter)->get(route('survey.compare', $s->survey))->assertOk();

        $s->survey->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
        $compare()->assertDontSee('Qualifikationsprozess abschliessen')->assertDontSee('Qualifikationsprozess abgeschlossen');

        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $compare()->assertSee('Qualifikationsprozess abschliessen');

        $this->actingAs($s->gruppenleiter)->patch(route('survey.finish', $s->survey->id));
        $compare()->assertDontSee('Qualifikationsprozess abschliessen')->assertSee('Qualifikationsprozess abgeschlossen');
    }

    public function test_resetting_to_new_clears_both_rounds()
    {
        $s = CampScenario::create();
        $question = $this->filledQuestion($s);

        $this->actingAs($s->kursleiter)->put(route('surveys.update', $s->survey->id), [
            'survey_status_id' => SurveyStatus::Neu->value,
            'update' => 1,
        ]);

        $question->refresh();
        $this->assertSame(SurveyStatus::Neu, $s->survey->fresh()->survey_status_id);
        $this->assertSame([$this->answer('0'), $this->answer('0')], [$question->answer_first_id, $question->answer_second_id]);
        $this->assertSame(['', ''], [$question->comment_first, $question->comment_second]);
        $this->assertSame($this->answer('-'), $question->answer_leader_id);
    }

    public function test_resetting_to_first_round_clears_only_second_round()
    {
        $s = CampScenario::create();
        $question = $this->filledQuestion($s);

        $this->actingAs($s->kursleiter)->put(route('surveys.update', $s->survey->id), [
            'survey_status_id' => SurveyStatus::ErsteRundeOffen->value,
            'update' => 1,
        ]);

        $question->refresh();
        $this->assertSame($this->answer('+'), $question->answer_first_id);
        $this->assertSame($this->answer('0'), $question->answer_second_id);
    }

    public function test_changing_status_without_reset_keeps_answers()
    {
        $s = CampScenario::create();
        $question = $this->filledQuestion($s);

        $this->actingAs($s->kursleiter)->put(route('surveys.update', $s->survey->id), [
            'survey_status_id' => SurveyStatus::Neu->value,
        ]);

        $question->refresh();
        $this->assertSame(SurveyStatus::Neu, $s->survey->fresh()->survey_status_id);
        $this->assertSame($this->answer('+'), $question->answer_first_id);
        $this->assertSame($this->answer('++'), $question->answer_second_id);
    }

    private function scenarioWith(array $campSettings, SurveyStatus $status): CampScenario
    {
        $s = CampScenario::create();
        $s->camp->forceFill($campSettings)->save();
        $s->survey->update(['survey_status_id' => $status]);

        return $s;
    }

    private function question(CampScenario $s): SurveyQuestion
    {
        return SurveyQuestion::findOrFail($s->survey->questions()->firstOrFail()->id);
    }

    private function filledQuestion(CampScenario $s): SurveyQuestion
    {
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $question = $this->question($s);
        $question->update([
            'answer_first_id' => $this->answer('+'), 'comment_first' => 'Erste',
            'answer_second_id' => $this->answer('++'), 'comment_second' => 'Zweite',
            'answer_leader_id' => $this->answer('-'),
        ]);

        return $question;
    }

    /** IDs der vorausgewählten Antworten einer Frage im Formular. */
    private function checkedAnswers(string $html, SurveyQuestion $question): array
    {
        preg_match_all('/<input[^>]*name="answers\['.$question->id.'\]"[^>]*>/', $html, $inputs);
        $checked = array_filter($inputs[0], fn ($input) => str_contains($input, 'checked'));

        return array_values(array_map(fn ($input) => (int) preg_replace('/.*value="(\d+)".*/s', '$1', $input), $checked));
    }

    /** Diagramm der TN-Person: 1. und 2. Selbsteinschätzung, die 2. noch leer (alles 0). */
    private function assertChartWithoutLeader(array $datasets): void
    {
        $this->assertSame(['1. Selbsteinschätzung', '2. Selbsteinschätzung'], array_column($datasets[0], 'label'));
        $this->assertSame([0], array_values(array_unique($datasets[0][1]['data'])));
    }

    private function answer(string $name): int
    {
        return Answer::where('name', $name)->value('id');
    }
}
