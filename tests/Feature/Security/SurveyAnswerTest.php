<?php

namespace Tests\Feature\Security;

use App\Enums\SurveyStatus;
use App\Models\Answer;
use App\Models\SurveyQuestion;
use Database\Seeders\DemoCampSeeder;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Antworten in einer Quali speichern (PATCH survey/{survey}) und Quali abschliessen (PATCH compare/{id}).
 */
class SurveyAnswerTest extends TestCase
{
    public function test_participant_saves_answers_of_own_quali()
    {
        $s = CampScenario::create();
        $question = $s->survey->questions()->firstOrFail();
        $plus = Answer::where('name', '+')->value('id');

        $this->actingAs($s->participant)
            ->patch(route('survey.update', $s->survey), ['answers' => [$question->id => $plus]]);

        $this->assertSame($plus, SurveyQuestion::findOrFail($question->id)->answer_first_id);
    }

    public function test_participant_cannot_save_answers_into_quali_of_another_participant()
    {
        $s = CampScenario::create();
        $foreignQuestion = $s->unassignedSurvey->questions()->firstOrFail();
        $plus = Answer::where('name', '+')->value('id');

        $this->actingAs($s->participant)
            ->patch(route('survey.update', $s->unassignedSurvey), ['answers' => [$foreignQuestion->id => $plus]])
            ->assertForbidden();

        $this->assertNotSame($plus, SurveyQuestion::findOrFail($foreignQuestion->id)->answer_first_id);
    }

    public function test_participant_cannot_smuggle_foreign_question_ids_into_own_quali()
    {
        $s = CampScenario::create();
        $foreignQuestion = $s->unassignedSurvey->questions()->firstOrFail();
        $plus = Answer::where('name', '+')->value('id');

        $this->actingAs($s->participant)
            ->patch(route('survey.update', $s->survey), [
                'answers' => [$foreignQuestion->id => $plus],
                'comments' => [$foreignQuestion->id => 'Untergeschoben'],
            ]);

        $foreignQuestion = SurveyQuestion::findOrFail($foreignQuestion->id);
        $this->assertNotSame($plus, $foreignQuestion->answer_first_id);
        $this->assertNotSame('Untergeschoben', $foreignQuestion->comment_first);
    }

    public function test_public_demo_login_cannot_change_answers_of_real_qualis()
    {
        $s = CampScenario::create();
        $this->seed(DemoCampSeeder::class);
        $realQuestion = $s->survey->questions()->firstOrFail();
        $plus = Answer::where('name', '+')->value('id');

        $fields = ['answer_first_id', 'answer_second_id', 'answer_leader_id', 'comment_first', 'comment_second'];
        $before = $realQuestion->only($fields);

        $this->get('/loginTN');
        $demoSurvey = auth()->user()->camp->surveys()->whereHas('campUser', fn ($q) => $q->where('user_id', auth()->id()))->firstOrFail();
        $this->patch(route('survey.update', $demoSurvey), [
            'answers' => [$realQuestion->id => $plus],
            'comments' => [$realQuestion->id => 'Untergeschoben'],
        ]);

        $this->assertSame($before, SurveyQuestion::findOrFail($realQuestion->id)->only($fields));
    }

    public function test_leader_of_foreign_camp_cannot_finish_quali()
    {
        $a = CampScenario::create();
        $b = CampScenario::create();
        $b->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($a->gruppenleiter)
            ->patch(route('survey.finish', $b->survey->id))
            ->assertForbidden();

        $this->assertSame(SurveyStatus::TnAbgeschlossen, $b->survey->fresh()->survey_status_id);
    }

    public function test_assigned_leader_finishes_quali()
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($s->gruppenleiter)
            ->from(route('survey.compare', $s->survey))
            ->patch(route('survey.finish', $s->survey->id))
            ->assertRedirect(route('survey.compare', $s->survey));

        $this->assertSame(SurveyStatus::Fertig, $s->survey->fresh()->survey_status_id);
    }
}
