<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Menü unten auf dem Handy (includes/bottomnav.blade.php): Punkte je Rolle. Bedienung im Browser:
 * Browser/MobileNavigationTest.
 */
class BottomNavigationTest extends TestCase
{
    public function test_guest_has_no_bottom_navigation(): void
    {
        $this->get('/login')->assertOk()->assertDontSee('id="bottom-navigation"', false);
    }

    public function test_participant_gets_overview_own_quali_and_account(): void
    {
        $s = CampScenario::create();

        $nav = $this->bottomNav($s->participant, '/home');

        $this->assertStringContainsString('aria-current="page"', $nav);
        $this->assertStringContainsString('href="'.route('survey.survey', $s->survey->slug).'"', $nav);
        $this->assertStringContainsString('href="'.route('home.user', $s->participant->slug).'"', $nav);
        foreach (['Feedback', 'Neue Rückmeldung', 'Kursadmin', 'Mehr'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $nav);
        }
    }

    public function test_participant_quali_link_goes_to_compare_after_closing(): void
    {
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $nav = $this->bottomNav($s->participant, '/home');

        $this->assertStringContainsString('href="'.route('survey.compare', $s->survey->slug).'"', $nav);
    }

    public function test_leader_gets_feedback_new_feedback_account_and_more(): void
    {
        $s = CampScenario::create();

        $nav = $this->bottomNav($s->gruppenleiter, '/posts');

        $this->assertStringContainsString('href="'.url('/posts').'?neu=1"', $nav);
        $this->assertStringContainsString('aria-label="Neue Rückmeldung"', $nav);
        $this->assertStringContainsString('href="'.route('home.user', $s->gruppenleiter->slug).'"', $nav);
        $this->assertStringContainsString('data-bottom-nav-more', $nav);
        $this->assertStringNotContainsString('Kursadmin', $nav);
        $this->assertMatchesRegularExpression('/aria-current="page"[^>]*>\s*<i class="fas fa-comments/', $nav);
    }

    public function test_campleader_gets_course_admin_instead_of_account(): void
    {
        $s = CampScenario::create();

        $nav = $this->bottomNav($s->kursleiter, '/admin');

        $this->assertStringContainsString('href="'.url('/admin').'"', $nav);
        $this->assertStringContainsString('Kursadmin', $nav);
        $this->assertStringNotContainsString(route('home.user', $s->kursleiter->slug), $nav);
    }

    private function bottomNav(User $user, string $url): string
    {
        $html = $this->actingAs($user)->get($url)->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<nav id="bottom-navigation".*?<\/nav>/s', $html);
        preg_match('/<nav id="bottom-navigation".*?<\/nav>/s', $html, $match);

        return $match[0];
    }
}
