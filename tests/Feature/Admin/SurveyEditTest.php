<?php

namespace Tests\Feature\Admin;

use App\Enums\SurveyStatus;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Quali bearbeiten im Admin-Bereich (Status setzen, Antworten zurücksetzen).
 */
class SurveyEditTest extends TestCase
{
    public function test_form_preselects_current_status()
    {
        // Sonst würde Speichern ohne Änderung still einen anderen Status setzen.
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($s->kursleiter)
            ->get(route('surveys.edit', $s->survey))
            ->assertOk()
            ->assertSee('<option value="'.SurveyStatus::TnAbgeschlossen->value.'" selected="selected">', false);
    }

    public function test_invalid_status_is_rejected()
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->put(route('surveys.update', $s->survey->id), ['survey_status_id' => 7])
            ->assertSessionHasErrors('survey_status_id');

        $this->assertSame(SurveyStatus::Neu, $s->survey->fresh()->survey_status_id);
    }

    public function test_overview_shows_status_name()
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::Fertig]);

        $this->actingAs($s->gruppenleiter)
            ->get('/home')
            ->assertOk()
            ->assertSee('Qualifikationsprozess Abgeschlossen');
    }
}
