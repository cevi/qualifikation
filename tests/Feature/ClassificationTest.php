<?php

namespace Tests\Feature;

use App\Enums\Classification;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Ampel (rot/gelb/grün) auf der Profilseite: PATCH changeClassifications/{id}/{color}.
 */
class ClassificationTest extends TestCase
{
    public function test_assigned_leader_changes_classification()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)
            ->patch(route('users.changeClassifications', ['id' => $s->participant->id, 'color' => Classification::Rot->value]))
            ->assertOk();

        $this->assertSame(Classification::Rot, $s->campUser($s->participant)->classification_id);
    }

    public function test_profile_marks_current_classification()
    {
        $s = CampScenario::create();
        $s->campUser($s->participant)->update(['classification_id' => Classification::Gruen]);

        $this->actingAs($s->gruppenleiter)
            ->get(route('home.profile', $s->participant))
            ->assertOk()
            ->assertSee('class="circle green"', false)
            ->assertDontSee('class="circle red"', false);
    }

    public function test_unknown_color_is_rejected()
    {
        $s = CampScenario::create();
        $s->campUser($s->participant)->update(['classification_id' => Classification::Gelb]);

        $this->actingAs($s->gruppenleiter)
            ->patch(route('users.changeClassifications', ['id' => $s->participant->id, 'color' => 99]))
            ->assertNotFound();

        $this->assertSame(Classification::Gelb, $s->campUser($s->participant)->classification_id);
    }

    public function test_other_leader_cannot_change_classification()
    {
        $s = CampScenario::create();
        $s->campUser($s->participant)->update(['classification_id' => Classification::Gelb]);
        $otherLeader = User::factory()->gruppenleiter()->inCamp($s->camp)->create();

        $this->actingAs($otherLeader)
            ->patch(route('users.changeClassifications', ['id' => $s->participant->id, 'color' => Classification::Rot->value]));

        $this->assertSame(Classification::Gelb, $s->campUser($s->participant)->classification_id);
    }
}
