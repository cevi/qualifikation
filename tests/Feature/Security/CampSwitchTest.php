<?php

namespace Tests\Feature\Security;

use App\Models\Camp;
use App\Models\CampUser;
use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * PUT /camps/{camp}: Wechsel des aktuellen Kurses über das Kurs-Dropdown.
 */
class CampSwitchTest extends TestCase
{
    public function test_user_can_switch_to_a_camp_they_belong_to()
    {
        $s = CampScenario::create();
        $otherCamp = Camp::factory()->create();
        CampUser::create([
            'camp_id' => $otherCamp->id,
            'user_id' => $s->gruppenleiter->id,
            'role_id' => config('status.role_Kursleiter'),
        ]);

        $this->actingAs($s->gruppenleiter)
            ->put(route('home.camps.update', $otherCamp->id))
            ->assertRedirect('/home');

        $gruppenleiter = $s->gruppenleiter->fresh();
        $this->assertSame($otherCamp->id, $gruppenleiter->camp_id);
        $this->assertSame(config('status.role_Kursleiter'), $gruppenleiter->role_id);
    }

    public function test_kursleiter_cannot_join_a_foreign_camp()
    {
        $foreign = CampScenario::create();
        $ownCamp = Camp::factory()->create();
        $kursleiter = User::factory()->kursleiter()->inCamp($ownCamp)->create();

        $this->actingAs($kursleiter)
            ->put(route('home.camps.update', $foreign->camp->id))
            ->assertForbidden();

        $this->assertSame($ownCamp->id, $kursleiter->fresh()->camp_id);
        $this->assertDatabaseMissing('camp_users', ['camp_id' => $foreign->camp->id, 'user_id' => $kursleiter->id]);
        $this->actingAs($kursleiter->fresh())
            ->get(route('survey.compare', $foreign->survey))
            ->assertRedirect('/home');
    }

    public function test_user_can_switch_back_to_global_camp()
    {
        $s = CampScenario::create();
        $globalCamp = Camp::where('global_camp', true)->firstOrFail();

        $this->actingAs($s->participant)
            ->put(route('home.camps.update', $globalCamp->id))
            ->assertRedirect('/home');

        $this->assertSame($globalCamp->id, $s->participant->fresh()->camp_id);
    }

    public function test_admin_can_switch_to_any_camp()
    {
        $s = CampScenario::create();

        $this->actingAs($s->admin)
            ->put(route('home.camps.update', $s->camp->id))
            ->assertRedirect('/home');

        $this->assertSame($s->camp->id, $s->admin->fresh()->camp_id);
    }
}
