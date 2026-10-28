<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\User;
use App\Services\CampChecks;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Regeln der Aufgabenliste auf der Übersicht (App\Services\CampChecks). Darstellung: SurveyOverviewTest.
 */
class CampChecksTest extends TestCase
{
    private CampScenario $s;

    private CampChecks $checks;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
        $this->checks = new CampChecks;
    }

    public function test_leader_sees_only_own_participants_and_campleader_all(): void
    {
        $this->assertSame([$this->s->survey->id], $this->checks->surveysFor($this->s->gruppenleiter)->pluck('id')->all());
        $this->assertEqualsCanonicalizing(
            [$this->s->survey->id, $this->s->unassignedSurvey->id],
            $this->checks->surveysFor($this->s->kursleiter)->pluck('id')->all(),
        );
        $this->assertSame([$this->s->survey->id], $this->checks->surveysFor($this->s->participant)->pluck('id')->all());
    }

    public function test_closed_self_assessment_is_an_urgent_task_for_the_leader(): void
    {
        $this->s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $tasks = $this->tasksFor($this->s->gruppenleiter);

        $this->assertCount(1, $tasks);
        $this->assertTrue($tasks[0]['urgent']);
        $this->assertSame(route('survey.compare', $this->s->survey->slug), $tasks[0]['link']);
    }

    public static function releases(): array
    {
        return [
            'ohne Status-Steuerung' => [false, null, true],
            'Steuerung, noch nicht freigegeben' => [true, null, false],
            'Steuerung, Kurs auf "Neu"' => [true, SurveyStatus::Neu, false],
            'Steuerung, 1. Runde freigegeben' => [true, SurveyStatus::ZweiteRundeOffen, true],
        ];
    }

    #[DataProvider('releases')]
    public function test_not_started_is_a_task_only_after_release(bool $statusControl, ?SurveyStatus $campStatus, bool $expected): void
    {
        $this->s->camp->update(['status_control' => $statusControl, 'survey_status_id' => $campStatus]);

        $titles = $this->tasksFor($this->s->gruppenleiter)->pluck('title');

        $this->assertSame($expected, $titles->contains($this->s->participant->username.' hat noch nicht begonnen'));
    }

    public function test_participant_task_is_the_own_open_self_assessment(): void
    {
        $this->actingAs($this->s->participant);
        $this->assertSame(['1. Selbsteinschätzung ausfüllen'], $this->tasksFor($this->s->participant)->pluck('title')->all());

        $this->s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->assertCount(0, $this->tasksFor($this->s->participant));
    }

    public function test_camp_tasks_only_for_campleader(): void
    {
        $leaderTitles = $this->tasksFor($this->s->gruppenleiter)->pluck('title');
        $campleaderTitles = $this->tasksFor($this->s->kursleiter)->pluck('title');

        $this->assertNotContains('1 Teilnehmende Person ohne Leitende', $leaderTitles);
        $this->assertContains('1 Teilnehmende Person ohne Leitende', $campleaderTitles);
    }

    public function test_participants_without_survey(): void
    {
        $this->assertCount(0, $this->checks->participantsWithoutSurvey($this->s->camp));

        User::factory()->inCamp($this->s->camp, $this->s->gruppenleiter)->create();

        $this->assertCount(1, $this->checks->participantsWithoutSurvey($this->s->camp));
        $this->assertContains('1 Teilnehmende Person ohne Qualifikation', $this->tasksFor($this->s->kursleiter)->pluck('title'));
    }

    public static function finishCases(): array
    {
        return [
            'vor 4 Monaten zu Ende' => [['end_date' => '-4 months'], true],
            'vor 2 Monaten zu Ende' => [['end_date' => '-2 months'], false],
            'schon abgeschlossen' => [['end_date' => '-4 months', 'finish' => true], false],
            'Demo-Kurs' => [['end_date' => '-4 months', 'demo' => true], false],
            'Global-Kurs' => [['end_date' => '-4 months', 'global_camp' => true], false],
            'ohne Schlussdatum' => [['end_date' => null], false],
            'Schlussdatum 0000-00-00' => [['end_date' => '0000-00-00'], false],
        ];
    }

    #[DataProvider('finishCases')]
    public function test_camp_must_be_finished_three_months_after_end_date(array $attributes, bool $expected): void
    {
        if (isset($attributes['end_date']) && $attributes['end_date'] !== '0000-00-00') {
            $attributes['end_date'] = now()->modify($attributes['end_date'])->toDateString();
        }
        $camp = Camp::factory()->make($attributes);

        $this->assertSame($expected, $this->checks->mustBeFinished($camp));
    }

    public function test_finishing_the_camp_is_an_urgent_campleader_task_on_top(): void
    {
        $this->s->camp->update(['end_date' => now()->subMonths(4)->toDateString()]);

        $tasks = $this->tasksFor($this->s->kursleiter);

        $this->assertSame('Kurs abschliessen', $tasks[0]['title']);
        $this->assertTrue($tasks[0]['urgent']);
        $this->assertSame(route('admin.camps.edit', $this->s->camp), $tasks[0]['link']);
    }

    public function test_second_round_can_be_released_when_all_have_closed_the_first(): void
    {
        $this->assertFalse($this->checks->secondRoundCanBeReleased($this->s->camp));

        $this->s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->assertFalse($this->checks->secondRoundCanBeReleased($this->s->camp), 'eine Quali ist noch offen');

        $this->s->unassignedSurvey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->assertTrue($this->checks->secondRoundCanBeReleased($this->s->camp->fresh()));

        $this->s->camp->update(['secondsurveyopen' => true]);
        $this->assertFalse($this->checks->secondRoundCanBeReleased($this->s->camp), 'schon freigegeben');
    }

    private function tasksFor(User $user)
    {
        $user = $user->fresh();

        return $this->checks->dashboardTasks($user, $this->checks->surveysFor($user));
    }
}
