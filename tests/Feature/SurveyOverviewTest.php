<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use Illuminate\Support\Str;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Übersicht nach dem Login (home/surveys.blade.php): auf dem Handy Karten, ab md eine Tabelle, beide mit denselben
 * Links je nach Rolle und Status.
 */
class SurveyOverviewTest extends TestCase
{
    public function test_participant_sees_own_survey_without_compare_and_profile_links(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)->get('/home')
            ->assertOk()
            ->assertSee(route('survey.survey', $s->survey->slug), false)
            ->assertDontSee(route('survey.compare', $s->survey->slug), false)
            ->assertDontSee(route('home.profile', $s->participant->slug), false)
            ->assertSee($s->participant->username);
    }

    public function test_participant_gets_compare_link_after_closing(): void
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($s->participant)->get('/home')
            ->assertOk()
            ->assertSee(route('survey.compare', $s->survey->slug), false)
            ->assertDontSee(route('survey.survey', $s->survey->slug), false);
    }

    public function test_leader_sees_compare_and_profile_links_in_cards_and_table(): void
    {
        $s = CampScenario::create();

        $html = $this->actingAs($s->gruppenleiter)->get('/home')->assertOk()->getContent();

        $cards = Str::between($html, 'space-y-3 md:hidden', '<table');
        $table = Str::between($html, '<table', '</table>');
        foreach ([$cards, $table] as $part) {
            $this->assertStringContainsString('href="'.route('survey.compare', $s->survey->slug).'"', $part);
            $this->assertStringContainsString('href="'.route('home.profile', $s->participant->slug).'"', $part);
            $this->assertStringContainsString('href="'.route('survey.survey', $s->survey->slug).'"', $part);
        }
        $this->assertStringContainsString('Teilnehmende</th>', $table);
    }
}
