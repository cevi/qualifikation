<?php

namespace Tests\Feature;

use App\Models\Camp;
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
}
