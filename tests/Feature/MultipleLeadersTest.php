<?php

namespace Tests\Feature;

use App\Enums\Classification;
use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\User;
use App\Services\CampChecks;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Mehrere gleichgestellte Leitende pro Teilnehmende Person (camp_user_leader): alle sehen und bearbeiten die Quali,
 * stellen die Ampel und schliessen ab. Leitende, die nicht zugeteilt sind, nicht.
 */
class MultipleLeadersTest extends TestCase
{
    private CampScenario $s;

    /** Zweite Leitende der Teilnehmenden Person $s->participant, neben $s->gruppenleiter. */
    private User $coLeader;

    /** Leitende im selben Kurs, aber nicht zugeteilt. */
    private User $otherLeader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
        $this->coLeader = User::factory()->gruppenleiter()->inCamp($this->s->camp)->create(['username' => 'Zora Co']);
        $this->otherLeader = User::factory()->gruppenleiter()->inCamp($this->s->camp)->create();
        $this->s->campUser($this->s->participant)->leaders()->attach($this->coLeader);
        $this->s->gruppenleiter->update(['username' => 'Anna Leitung']);
    }

    public function test_factory_assigns_several_leaders(): void
    {
        $participant = User::factory()->inCamp($this->s->camp, [$this->s->gruppenleiter, $this->coLeader])->create();

        $campUser = $this->s->campUser($participant);
        $this->assertTrue($campUser->isLedBy($this->s->gruppenleiter));
        $this->assertTrue($campUser->isLedBy($this->coLeader));
        $this->assertFalse($campUser->isLedBy($this->otherLeader));
    }

    public function test_every_assigned_leader_opens_and_fills_in_the_quali(): void
    {
        foreach ([$this->s->gruppenleiter, $this->coLeader] as $leader) {
            $this->actingAs($leader)->get(route('survey.compare', $this->s->survey))->assertOk();
            $this->actingAs($leader)->get(route('survey.survey', $this->s->survey))->assertOk();
        }
    }

    public function test_leader_who_is_not_assigned_has_no_access(): void
    {
        $this->actingAs($this->otherLeader)->get(route('survey.compare', $this->s->survey))->assertRedirect('/home');
        $this->actingAs($this->otherLeader)->get(route('survey.survey', $this->s->survey))->assertRedirect('/home');
    }

    public function test_every_assigned_leader_changes_classification(): void
    {
        $campUser = $this->s->campUser($this->s->participant);

        $this->actingAs($this->coLeader)
            ->patch(route('users.changeClassifications', ['id' => $this->s->participant->id, 'color' => Classification::Rot->value]))
            ->assertOk();
        $this->assertSame(Classification::Rot, $campUser->fresh()->classification_id);

        $this->actingAs($this->otherLeader)
            ->patch(route('users.changeClassifications', ['id' => $this->s->participant->id, 'color' => Classification::Gruen->value]));
        $this->assertSame(Classification::Rot, $campUser->fresh()->classification_id);
    }

    public function test_every_assigned_leader_may_finish_the_quali(): void
    {
        $this->s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);

        $this->actingAs($this->coLeader)->get(route('survey.compare', $this->s->survey))
            ->assertSee('Qualifikationsprozess abschliessen');
        $this->actingAs($this->coLeader)->patch(route('survey.finish', $this->s->survey->id));

        $this->assertSame(SurveyStatus::Fertig, $this->s->survey->fresh()->survey_status_id);
    }

    public function test_navigation_lists_participant_under_own_participants_of_each_leader(): void
    {
        $this->actingAs($this->coLeader);
        $this->assertSame([$this->s->participant->id], $this->freshCamp()->my_participants->modelKeys());
        $this->assertNotContains($this->s->participant->id, $this->freshCamp()->other_participants->modelKeys());

        $this->actingAs($this->otherLeader);
        $this->assertSame([], $this->freshCamp()->my_participants->modelKeys());
        $this->assertContains($this->s->participant->id, $this->freshCamp()->other_participants->modelKeys());
    }

    public function test_overview_names_the_other_leaders(): void
    {
        $this->actingAs($this->coLeader)->get('/home')
            ->assertOk()
            ->assertSee($this->s->participant->username)
            ->assertSee('Zusammen mit Anna Leitung');

        $this->actingAs($this->s->kursleiter)->get('/home')
            ->assertOk()
            ->assertSee('Leitende: Anna Leitung, Zora Co');
    }

    public function test_tasks_count_participant_for_each_leader(): void
    {
        $checks = app(CampChecks::class);
        $this->s->post->delete();

        $this->assertSame([$this->s->survey->id], $checks->surveysFor($this->coLeader)->modelKeys());
        $this->assertCount(1, $checks->participantsWithoutPost($this->s->camp, $this->coLeader));
        $this->assertCount(0, $checks->participantsWithoutPost($this->s->camp, $this->otherLeader));
        // Nur $s->unassignedParticipant ist ohne Leitende.
        $this->assertCount(1, $checks->participantsWithoutLeader($this->s->camp));
    }

    public function test_admin_tables_list_all_leaders(): void
    {
        $this->actingAs($this->s->kursleiter);

        $user = collect($this->getJson(route('users.table'))->json('data'))->firstWhere('id', $this->s->participant->id);
        $survey = collect($this->getJson(route('surveys.table'))->json('data'))->firstWhere('id', $this->s->survey->id);

        $this->assertSame('Anna Leitung, Zora Co', $user['leader']);
        $this->assertSame('Anna Leitung, Zora Co', $survey['leader']);
    }

    public function test_profile_and_account_page_show_all_leaders(): void
    {
        $this->actingAs($this->otherLeader)->get(route('home.profile', $this->s->participant))
            ->assertOk()
            ->assertSee('Anna Leitung, Zora Co');
        $this->actingAs($this->s->participant)->get(route('home.user', $this->s->participant))
            ->assertOk()
            ->assertSee('value="Anna Leitung, Zora Co"', false);
    }

    public function test_edit_form_checks_current_leaders(): void
    {
        $this->actingAs($this->s->kursleiter)->get(route('admin.users.edit', $this->s->participant))
            ->assertOk()
            ->assertSee('name="leader_ids[]" value="'.$this->coLeader->id.'" id="leader_'.$this->coLeader->id.'" class="field-check" checked', false)
            ->assertDontSee('value="'.$this->otherLeader->id.'" id="leader_'.$this->otherLeader->id.'" class="field-check" checked', false);
    }

    public function test_campleader_assigns_several_leaders(): void
    {
        $this->update(['leader_ids' => [$this->coLeader->id, $this->otherLeader->id]])->assertRedirect('/admin/users');

        $this->assertEqualsCanonicalizing(
            [$this->coLeader->id, $this->otherLeader->id],
            $this->s->campUser($this->s->participant)->leaders->modelKeys()
        );
    }

    public function test_unchecking_all_leaders_removes_them(): void
    {
        // So sendet das Formular, wenn nichts angekreuzt ist (verstecktes Feld leader_ids).
        $this->update(['leader_ids' => ''])->assertRedirect('/admin/users');

        $this->assertTrue($this->s->campUser($this->s->participant)->leaders->isEmpty());
    }

    public function test_update_without_leader_field_keeps_leaders(): void
    {
        $this->update([])->assertRedirect('/admin/users');

        $this->assertCount(2, $this->s->campUser($this->s->participant)->leaders);
    }

    public function test_leader_of_another_camp_cannot_be_assigned(): void
    {
        $foreign = CampScenario::create()->gruppenleiter;

        $this->update(['leader_ids' => [$foreign->id]])->assertSessionHasErrors('leader_ids.0');

        $this->assertFalse($this->s->campUser($this->s->participant)->isLedBy($foreign));
    }

    public function test_new_person_gets_the_chosen_leaders(): void
    {
        $this->actingAs($this->s->kursleiter)->post(route('admin.users.store'), [
            'username' => 'Neue Person',
            'email' => 'neu@example.com',
            'password' => 'startpasswort',
            'role_id' => Role::Teilnehmer->value,
            'leader_ids' => [$this->s->gruppenleiter->id, $this->coLeader->id],
        ])->assertSessionHasNoErrors();

        $person = User::where('email', 'neu@example.com')->firstOrFail();
        $this->assertCount(2, $this->s->campUser($person)->leaders);
    }

    public function test_removing_a_leader_from_the_camp_unassigns_them(): void
    {
        $this->actingAs($this->s->kursleiter)->delete(route('admin.users.destroy', $this->coLeader));

        $campUser = $this->s->campUser($this->s->participant);
        $this->assertFalse($campUser->isLedBy($this->coLeader));
        $this->assertTrue($campUser->isLedBy($this->s->gruppenleiter));
    }

    public function test_leaders_are_per_camp(): void
    {
        // Gleiche Person in einem zweiten Kurs: dort ohne die Leitenden aus diesem Kurs.
        $other = Camp::factory()->create();
        $campUser = CampUser::create(['camp_id' => $other->id, 'user_id' => $this->s->participant->id]);

        $this->assertTrue($campUser->leaders->isEmpty());
        $this->assertCount(2, $this->s->campUser($this->s->participant)->leaders);
    }

    private function update(array $data)
    {
        return $this->actingAs($this->s->kursleiter)->put(route('admin.users.update', $this->s->participant->id), $data + [
            'username' => $this->s->participant->username,
            'email' => $this->s->participant->email,
        ]);
    }

    private function freshCamp(): Camp
    {
        return Camp::findOrFail($this->s->camp->id);
    }
}
