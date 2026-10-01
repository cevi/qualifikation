<?php

namespace Tests\Feature\Admin;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\CampType;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * "Qualis erstellen" unter Admin → Qualis (AdminSurveysController@store).
 */
class SurveyGenerationTest extends TestCase
{
    public function test_quali_has_all_default_chapters_and_questions_for_default_camp_type()
    {
        $s = CampScenario::create();
        $s->camp->camp_type->forceFill(['default_type' => true])->save();
        Chapter::whereIn('number', [1, 2])->update(['default_chapter' => true]);
        $defaultChapterIds = Chapter::where('default_chapter', true)->pluck('id')->sort()->values()->all();
        $newParticipant = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();

        $this->actingAs($s->kursleiter)
            ->post(route('surveys.store'))
            ->assertOk();

        $survey = Survey::where('camp_user_id', $s->campUser($newParticipant)->id)->sole();
        $this->assertSame(config('status.survey_neu'), $survey->survey_status_id);
        $this->assertNotEmpty($survey->slug);
        $this->assertSame($defaultChapterIds, $survey->chapters->pluck('chapter_id')->sort()->values()->all());
        $this->assertSame(
            Question::whereIn('chapter_id', $defaultChapterIds)->pluck('id')->sort()->values()->all(),
            $survey->questions->pluck('question_id')->sort()->values()->all()
        );
    }

    public function test_quali_uses_chapters_of_custom_camp_type()
    {
        $s = CampScenario::create();
        $campType = CampType::create(['name' => 'Eigener Kurstyp', 'user_id' => $s->kursleiter->id]);
        $chapter = Chapter::create(['number' => 1, 'name' => 'Eigenes Kapitel', 'camp_type_id' => $campType->id]);
        $question = Question::create([
            'name' => 'Eigene Frage',
            'number' => '1.1',
            'competence' => 'Eigene Kompetenz',
            'chapter_id' => $chapter->id,
            'camp_type_id' => $campType->id,
            'sort-index' => 1,
        ]);
        $s->camp->update(['camp_type_id' => $campType->id]);
        $newParticipant = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();

        $this->actingAs($s->kursleiter)->post(route('surveys.store'));

        $survey = Survey::where('camp_user_id', $s->campUser($newParticipant)->id)->sole();
        $this->assertSame([$chapter->id], $survey->chapters->pluck('chapter_id')->all());
        $this->assertSame([$question->id], $survey->questions->pluck('question_id')->all());
    }

    public function test_all_answers_start_with_answer_zero()
    {
        $s = CampScenario::create();
        $s->camp->camp_type->forceFill(['default_type' => true])->save();
        Chapter::query()->update(['default_chapter' => true]);
        $newParticipant = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();

        $this->actingAs($s->kursleiter)->post(route('surveys.store'));

        $zero = Answer::where('name', '0')->value('id');
        $questions = Survey::where('camp_user_id', $s->campUser($newParticipant)->id)->sole()->questions;
        $this->assertNotEmpty($questions);
        foreach ($questions as $question) {
            $this->assertSame([$zero, $zero, $zero], [$question->answer_first_id, $question->answer_second_id, $question->answer_leader_id]);
        }
    }

    public function test_quali_is_created_only_for_participants_without_one()
    {
        $s = CampScenario::create();
        $newParticipant = User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();

        $this->actingAs($s->kursleiter)->post(route('surveys.store'));

        $this->assertSame(1, Survey::where('camp_user_id', $s->campUser($newParticipant)->id)->count());
        $this->assertSame(1, Survey::where('camp_user_id', $s->campUser($s->participant)->id)->count());
        $this->assertSame(0, Survey::where('camp_user_id', $s->campUser($s->gruppenleiter)->id)->count());
        $this->assertSame(0, Survey::where('camp_user_id', $s->campUser($s->kursleiter)->id)->count());
    }

    public function test_quali_is_created_only_in_current_camp()
    {
        $s = CampScenario::create();
        $otherCamp = Camp::factory()->create();
        $otherParticipant = User::factory()->inCamp($otherCamp)->create();
        $before = Survey::count();

        $this->actingAs($s->kursleiter)->post(route('surveys.store'));

        $this->assertSame($before, Survey::count());
        $this->assertDatabaseMissing('surveys', ['camp_user_id' => $otherParticipant->id]);
    }

    public function test_demo_user_creates_no_quali()
    {
        $s = CampScenario::create();
        $s->kursleiter->forceFill(['demo' => true])->save();
        User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();
        $before = Survey::count();

        $this->actingAs($s->kursleiter)
            ->post(route('surveys.store'))
            ->assertOk();

        $this->assertSame($before, Survey::count());
    }

    public function test_admin_in_global_camp_creates_no_quali()
    {
        $s = CampScenario::create();
        $before = Survey::count();

        $this->actingAs($s->admin)
            ->post(route('surveys.store'))
            ->assertOk();

        $this->assertSame($before, Survey::count());
    }

    public function test_quali_is_not_created_by_a_get_request()
    {
        $s = CampScenario::create();
        User::factory()->inCamp($s->camp, $s->gruppenleiter)->create();
        $before = Survey::count();

        $this->actingAs($s->kursleiter)->get('/admin/surveys/create');

        $this->assertSame($before, Survey::count());
    }

    public function test_button_on_quali_overview_sends_post_to_store_route()
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->get(route('surveys.index'))
            ->assertOk()
            ->assertSeeInOrder(['url : "' . route('surveys.store') . '"', "type: 'POST'"], false);
    }
}
