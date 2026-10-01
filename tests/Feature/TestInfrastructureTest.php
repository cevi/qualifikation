<?php

namespace Tests\Feature;

use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

/**
 * Stellt sicher, dass Test-DB, Seeder und Factories so funktionieren, wie die übrigen Tests es voraussetzen.
 */
class TestInfrastructureTest extends TestCase
{
    public function test_runs_against_sqlite_in_memory()
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_base_data_is_seeded()
    {
        $this->assertSame(5, Role::count());
        $this->assertTrue(User::find(1)->isAdmin());
        $this->assertTrue(Camp::where('global_camp', true)->exists());
    }

    public function test_new_camp_gets_admin_camp_user()
    {
        $camp = Camp::factory()->create();

        $this->assertDatabaseHas('camp_users', [
            'camp_id' => $camp->id,
            'user_id' => 1,
            'role_id' => config('status.role_Administrator'),
        ]);
    }

    public function test_user_factory_in_camp_creates_camp_user()
    {
        $camp = Camp::factory()->create();
        $leader = User::factory()->gruppenleiter()->inCamp($camp)->create();
        $participant = User::factory()->inCamp($camp, $leader)->create();

        $this->assertTrue($leader->isLeader());
        $this->assertTrue($participant->isTeilnehmer());
        $this->assertSame($camp->id, $participant->camp->id);
        $this->assertDatabaseHas('camp_users', [
            'camp_id' => $camp->id,
            'user_id' => $participant->id,
            'role_id' => config('status.role_Teilnehmer'),
            'leader_id' => $leader->id,
        ]);
        $this->assertSame(1, CampUser::where('user_id', $leader->id)->count());
    }

    public function test_leader_can_open_participant_profile()
    {
        $camp = Camp::factory()->create();
        $leader = User::factory()->gruppenleiter()->inCamp($camp)->create();
        $participant = User::factory()->inCamp($camp, $leader)->create();

        $this->actingAs($leader)
            ->get(route('home.profile', $participant))
            ->assertOk()
            ->assertSee($participant->username);
    }
}
