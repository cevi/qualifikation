<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Übersicht nach dem Login (home/surveys.blade.php): Aufgaben (App\Services\CampChecks, siehe CampChecksTest),
 * Zähler und eine Karte pro Quali mit Links je nach Rolle und Status.
 */
class SurveyOverviewTest extends TestCase
{
    public function test_participant_sees_own_survey_and_task_without_compare_and_profile_links(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)->get('/home')
            ->assertOk()
            ->assertSee('Meine Qualifikation')
            ->assertSee('1. Selbsteinschätzung ausfüllen')
            ->assertSee(route('survey.survey', $s->survey->slug), false)
            ->assertDontSee(route('survey.compare', $s->survey->slug), false)
            ->assertDontSee(route('home.profile', $s->participant->slug), false)
            ->assertDontSee('Zum Abschliessen');
    }

    public function test_participant_gets_compare_link_and_no_task_after_closing(): void
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($s->participant)->get('/home')
            ->assertOk()
            ->assertSee(route('survey.compare', $s->survey->slug), false)
            ->assertDontSee(route('survey.survey', $s->survey->slug), false)
            ->assertDontSee('ausfüllen');
    }

    public function test_leader_sees_own_participants_with_links_and_counters(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->get('/home')
            ->assertOk()
            ->assertSee('Meine Teilnehmenden')
            ->assertSee($s->participant->username)
            ->assertDontSee($s->unassignedParticipant->username.'</a>', false)
            ->assertSee('href="'.route('survey.compare', $s->survey->slug).'"', false)
            ->assertSee('href="'.route('home.profile', $s->participant->slug).'?neu=1"', false)
            ->assertSee('1 Rückmeldung')
            ->assertSee('Zum Abschliessen');
    }

    public function test_closed_self_assessment_is_counted_but_no_task(): void
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $html = $this->actingAs($s->gruppenleiter)->get('/home')
            ->assertOk()
            ->assertDontSee('id="aufgaben"', false)
            ->assertDontSee('hat die Selbsteinschätzung abgeschlossen')
            ->getContent();
        $this->assertMatchesRegularExpression('/>1<\/span>\s*<span[^>]*>Zum Abschliessen</', $html);
    }

    public function test_campleader_sees_all_participants_and_camp_tasks(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->get('/home')
            ->assertOk()
            ->assertDontSee('Meine Teilnehmenden')
            ->assertSee($s->participant->username)
            ->assertSee($s->unassignedParticipant->username)
            ->assertSee('Noch ohne Leitende')
            ->assertSee('1 Teilnehmende Person ohne Leitende')
            ->assertSee('1 Teilnehmende Person ohne Rückmeldung')
            ->assertSee('text-orange-700', false)
            ->assertSee('(wichtig)');
    }

    public function test_overview_does_not_load_chart_data(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->get('/home')
            ->assertOk()
            ->assertDontSee('radarChart-', false);
    }
}
