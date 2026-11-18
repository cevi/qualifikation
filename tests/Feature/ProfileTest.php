<?php

namespace Tests\Feature;

use App\Models\Camp;
use App\Models\Post;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_profile_of_user_outside_own_camp_redirects_back()
    {
        $s = CampScenario::create();

        // Der Admin ist im Global-Camp, der Teilnehmer nicht.
        $this->actingAs($s->admin)
            ->from('/home')
            ->get(route('home.profile', $s->participant))
            ->assertRedirect('/home');
    }

    public function test_profile_navigates_between_participants()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)
            ->get(route('home.profile', $s->participant))
            ->assertOk()
            ->assertViewHas('previousUser', fn ($user) => $user->is($s->unassignedParticipant))
            ->assertViewHas('nextUser', fn ($user) => $user->is($s->unassignedParticipant));
    }

    public function test_profile_renders_when_camp_has_no_participants()
    {
        $camp = Camp::factory()->create();
        $kursleiter = User::factory()->kursleiter()->inCamp($camp)->create();
        $gruppenleiter = User::factory()->gruppenleiter()->inCamp($camp)->create();

        $this->actingAs($kursleiter)
            ->get(route('home.profile', $gruppenleiter))
            ->assertOk()
            ->assertViewHas('previousUser', null)
            ->assertViewHas('nextUser', null);
    }

    public function test_profile_shows_everything_in_one_header_and_two_cards(): void
    {
        $s = CampScenario::create();
        $long = str_repeat('Sehr ausführliche Beobachtung aus dem Lager. ', 8);
        $post = Post::factory()->for($s->campUser($s->participant), 'campUser')->for($s->gruppenleiter, 'leader')
            ->create(['camp_id' => $s->camp->id, 'comment' => $long]);

        $html = $this->actingAs($s->gruppenleiter)->get(route('home.profile', $s->participant))->assertOk()
            ->assertSee('data-photo-open="profil-foto"', false)
            ->assertSee('<dialog id="profil-foto"', false)
            ->assertSee(route('survey.survey', $s->survey->slug))
            ->assertSee(route('survey.compare', $s->survey->slug))
            ->assertSee('id="ansicht-radar"', false)
            ->assertSee('id="mehr-'.$post->id.'"', false)
            ->assertDontSee('id="mehr-'.$s->post->id.'"', false)
            ->getContent();

        // Vor/Zurück nur noch einmal, «Neue Rückmeldung» im Kopf der Karte
        $this->assertSame(1, substr_count($html, 'aria-label="Andere Teilnehmende"'));
        $card = substr($html, strpos($html, '>Rückmeldungen</h2>'), 1500);
        $this->assertStringContainsString('id="new-feedback-btn"', $card);
    }
}
