<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\Survey;
use Database\Seeders\DemoCampSeeder;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * task:daily (läuft täglich über den Scheduler) setzt den öffentlichen Demo-Kurs zurück.
 */
class DailyTaskTest extends TestCase
{
    public function test_demo_camp_is_reset()
    {
        $this->seed(DemoCampSeeder::class);
        $demo = Camp::where('demo', true)->firstOrFail();
        $demo->forceFill(['secondsurveyopen' => true, 'status_control' => true, 'survey_status_id' => SurveyStatus::ZweiteRundeOffen])->save();
        $demo->surveys()->update(['survey_status_id' => SurveyStatus::Fertig, 'comment' => 'Von Besuchern geändert']);

        $this->artisan('task:daily')->assertSuccessful();

        $demo->refresh();
        $this->assertFalse($demo->secondsurveyopen);
        $this->assertFalse($demo->status_control);
        $this->assertNull($demo->survey_status_id);
        foreach ($demo->surveys()->get() as $survey) {
            $this->assertSame(SurveyStatus::Neu, $survey->survey_status_id);
            $this->assertNotSame('Von Besuchern geändert', $survey->comment);
        }
    }

    public function test_real_camps_are_not_touched()
    {
        $this->seed(DemoCampSeeder::class);
        $s = CampScenario::create();
        $s->camp->update(['secondsurveyopen' => true]);
        $s->survey->update(['survey_status_id' => SurveyStatus::Fertig, 'comment' => 'Echte Einschätzung']);
        $question = $s->survey->questions()->firstOrFail();
        $question->update(['comment_first' => 'Echter Kommentar']);
        $post = $s->post->comment;

        $this->artisan('task:daily')->assertSuccessful();

        $this->assertTrue($s->camp->fresh()->secondsurveyopen);
        $survey = Survey::findOrFail($s->survey->id);
        $this->assertSame(SurveyStatus::Fertig, $survey->survey_status_id);
        $this->assertSame('Echte Einschätzung', $survey->comment);
        $this->assertSame('Echter Kommentar', $survey->questions()->findOrFail($question->id)->comment_first);
        $this->assertSame($post, $s->post->fresh()->comment);
    }
}
