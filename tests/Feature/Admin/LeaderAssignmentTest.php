<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Leitende zuteilen (admin/users/leaders): Matrix Teilnehmende × Leitende, jedes Kreuz speichert sofort per PUT/DELETE.
 */
class LeaderAssignmentTest extends TestCase
{
    private CampScenario $s;

    private User $coLeader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
        $this->coLeader = User::factory()->gruppenleiter()->inCamp($this->s->camp)->create(['username' => 'Zora Co']);
    }

    private function props(): array
    {
        $html = $this->actingAs($this->s->kursleiter)->get(route('admin.leaders.index'))->assertOk()->getContent();
        preg_match('/data-vue-component="leader-matrix" data-props="([^"]+)"/', $html, $match);

        return json_decode(html_entity_decode($match[1]), true);
    }

    public function test_people_overview_links_to_the_assignment(): void
    {
        $this->actingAs($this->s->kursleiter)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(route('admin.leaders.index'))
            ->assertSee('Leitende zuteilen');
    }

    public function test_page_lists_participants_with_their_leaders_and_all_possible_leaders(): void
    {
        $props = $this->props();

        $participants = collect($props['participants'])->keyBy('name');
        $this->assertEqualsCanonicalizing(
            [$this->s->participant->username, $this->s->unassignedParticipant->username],
            $participants->keys()->all(),
            'Nur Teilnehmende, keine Leitenden',
        );
        $this->assertSame([$this->s->gruppenleiter->id], $participants[$this->s->participant->username]['leader_ids']);
        $this->assertSame([], $participants[$this->s->unassignedParticipant->username]['leader_ids']);
        $this->assertSame($this->s->campUser($this->s->participant)->id, $participants[$this->s->participant->username]['id']);

        $this->assertEqualsCanonicalizing(
            [$this->s->kursleiter->id, $this->s->gruppenleiter->id, $this->coLeader->id],
            collect($props['leaders'])->pluck('id')->all(),
        );
        $this->assertSame(url('admin/users/leaders'), $props['url']);
    }

    public function test_put_assigns_and_delete_removes_a_leader(): void
    {
        $campUser = $this->s->campUser($this->s->unassignedParticipant);
        $url = route('admin.leaders.assign', [$campUser, $this->coLeader->id]);

        $this->actingAs($this->s->kursleiter)->putJson($url)->assertOk()->assertExactJson(['assigned' => true]);
        $this->actingAs($this->s->kursleiter)->putJson($url)->assertOk()->assertExactJson(['assigned' => true]);
        $this->assertSame([$this->coLeader->id], $campUser->fresh()->leaders->modelKeys(), 'Zweimal zuteilen gibt keinen doppelten Eintrag');

        $this->actingAs($this->s->kursleiter)->deleteJson($url)->assertOk()->assertExactJson(['assigned' => false]);
        $this->assertSame([], $campUser->fresh()->leaders->modelKeys());
    }

    public function test_additional_leader_keeps_the_existing_one(): void
    {
        $campUser = $this->s->campUser($this->s->participant);

        $this->actingAs($this->s->kursleiter)->putJson(route('admin.leaders.assign', [$campUser, $this->coLeader->id]))->assertOk();

        $this->assertEqualsCanonicalizing([$this->s->gruppenleiter->id, $this->coLeader->id], $campUser->fresh()->leaders->modelKeys());
    }

    public function test_only_leaders_of_the_camp_can_be_assigned(): void
    {
        $campUser = $this->s->campUser($this->s->unassignedParticipant);
        $foreignLeader = CampScenario::create()->gruppenleiter;

        foreach ([$this->s->participant, $foreignLeader] as $notALeader) {
            $this->actingAs($this->s->kursleiter)
                ->putJson(route('admin.leaders.assign', [$campUser, $notALeader->id]))
                ->assertUnprocessable();
        }
        $this->assertSame([], $campUser->fresh()->leaders->modelKeys());
    }

    public function test_group_leader_cannot_assign(): void
    {
        $campUser = $this->s->campUser($this->s->unassignedParticipant);

        $this->actingAs($this->s->gruppenleiter)->get(route('admin.leaders.index'))->assertRedirect('/home');
        $this->actingAs($this->s->gruppenleiter)
            ->putJson(route('admin.leaders.assign', [$campUser, $this->s->gruppenleiter->id]))
            ->assertRedirect('/home');
        $this->assertSame([], $campUser->fresh()->leaders->modelKeys());
    }

    public function test_demo_user_changes_nothing(): void
    {
        $this->s->kursleiter->forceFill(['demo' => true])->save();
        $campUser = $this->s->campUser($this->s->unassignedParticipant);

        $this->actingAs($this->s->kursleiter)
            ->putJson(route('admin.leaders.assign', [$campUser, $this->coLeader->id]))
            ->assertOk()
            ->assertExactJson(['assigned' => false]);
        $this->assertSame([], $campUser->fresh()->leaders->modelKeys());
    }
}
