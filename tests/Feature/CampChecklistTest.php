<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\StandardText;
use App\Models\User;
use App\Services\CampChecklist;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * «Erste Schritte» auf der Übersicht der Kursleitung (App\Services\CampChecklist, x-dashboard.checklist).
 * Entscheide des Users (November 2026): nur Kursleitung, Leitende und Teilnehmende ein Schritt, nur Standardtexte
 * optional, Aufgabenliste bleibt unverändert, Karte verschwindet nach dem Kursabschluss.
 */
class CampChecklistTest extends TestCase
{
    public function test_new_camp_has_only_the_first_step_done(): void
    {
        $camp = $this->newCamp();

        $checklist = $this->checklist($camp);

        $this->assertSame(['camp'], $checklist['steps']->where('done', true)->pluck('key')->all());
        $this->assertSame('people', $checklist['steps']->firstWhere('next', true)['key']);
        $this->assertSame([1, 8], [$checklist['done'], $checklist['total']]);
    }

    public function test_steps_follow_the_course_from_preparation_to_the_end(): void
    {
        $s = CampScenario::create();
        // Szenario: zwei Teilnehmende mit Quali, eine Person ohne Leitende, eine ohne Rückmeldung
        $this->assertSame('leaders', $this->next($s->camp));
        $this->assertSame('1 von 2 zugeteilt', $this->step($s->camp, 'leaders')['detail']);
        $this->assertSame('1 leitende Person · 2 Teilnehmende', $this->step($s->camp, 'people')['detail']);

        $s->campUser($s->unassignedParticipant)->leaders()->attach($s->gruppenleiter->id);
        $this->assertSame('posts', $this->next($s->camp), 'Qualis gibt es im Szenario schon');

        $s->post->replicate()->fill(['camp_user_id' => $s->campUser($s->unassignedParticipant)->id])->save();
        $this->assertSame('second-round', $this->next($s->camp));

        $s->camp->update(['secondsurveyopen' => true]);
        $this->assertSame('finish-surveys', $this->next($s->camp));
        $this->assertSame('0 von 2 abgeschlossen · im Gespräch mit den Leitenden', $this->step($s->camp, 'finish-surveys')['detail']);

        $s->camp->surveys()->each(fn ($survey) => $survey->update(['survey_status_id' => SurveyStatus::Fertig]));
        $this->assertSame('finish-camp', $this->next($s->camp));
        $this->assertSame(7, $this->checklist($s->camp)['done']);
    }

    public function test_first_round_step_only_with_status_control(): void
    {
        $s = CampScenario::create();
        $this->assertNull($this->step($s->camp, 'first-round'));

        $s->camp->update(['status_control' => true, 'survey_status_id' => SurveyStatus::Neu]);
        $this->assertFalse($this->step($s->camp, 'first-round')['done']);
        $this->assertSame(9, $this->checklist($s->camp)['total']);

        $s->camp->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
        $this->assertTrue($this->step($s->camp, 'first-round')['done']);
    }

    public function test_only_the_next_step_and_open_optional_steps_have_buttons(): void
    {
        $camp = $this->newCamp();

        $withButtons = $this->checklist($camp)['steps']->filter(fn (array $step) => $step['actions'])->pluck('key')->all();

        $this->assertSame(['people', 'texts'], $withButtons);
    }

    public function test_optional_standard_texts_do_not_count(): void
    {
        $camp = $this->newCamp();
        $before = $this->checklist($camp);

        StandardText::create(['title' => 'Lob', 'content' => 'Gut gemacht', 'camp_id' => $camp->id]);
        $after = $this->checklist($camp);

        $this->assertTrue($this->step($camp, 'texts')['done']);
        $this->assertSame('1 Text', $this->step($camp, 'texts')['detail']);
        $this->assertSame([$before['done'], $before['total']], [$after['done'], $after['total']]);
        $this->assertSame([], $this->step($camp, 'texts')['actions'], 'erledigt: kein Knopf mehr');
    }

    public function test_missing_end_date_leaves_the_first_step_open(): void
    {
        $camp = $this->newCamp(['end_date' => null]);

        $this->assertFalse($this->step($camp, 'camp')['done']);
        $this->assertSame('camp', $this->next($camp));
    }

    public function test_db_import_is_mentioned_only_when_possible(): void
    {
        config(['app.import_db' => true]);
        $camp = $this->newCamp();
        $this->assertSame('Teilnehmendenliste hochladen oder einzeln erfassen', $this->step($camp, 'people')['detail']);

        $camp->group->update(['api_token' => 'geheim']);
        $camp->update(['foreign_id' => 1234]);
        $this->assertSame('Aus der Cevi-DB oder als Teilnehmendenliste hochladen', $this->step($camp->fresh(), 'people')['detail']);
    }

    public function test_dashboard_shows_the_card_only_to_the_campleader_of_an_open_camp(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->get('/home')->assertOk()->assertSee('Erste Schritte')
            ->assertSee('Teilnehmende den Leitenden zuteilen');
        $this->actingAs($s->gruppenleiter)->get('/home')->assertOk()->assertDontSee('Erste Schritte');
        $this->actingAs($s->participant)->get('/home')->assertOk()->assertDontSee('Erste Schritte');

        $s->camp->update(['finish' => true]);
        $this->actingAs($s->kursleiter->fresh())->get('/home')->assertOk()->assertDontSee('Erste Schritte');
    }

    public function test_task_list_stays_next_to_the_card(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->get('/home')
            ->assertSee('Erste Schritte')
            ->assertSee('1 Teilnehmende Person ohne Leitende');
    }

    public function test_post_actions_return_to_the_dashboard(): void
    {
        $camp = $this->newCamp();
        $kursleiter = $camp->user;
        $this->actingAs($kursleiter)->get('/home')->assertSee('href="'.route('admin.users.index').'#import"', false);

        User::factory()->inCamp($camp, $kursleiter)->create();
        $this->get('/home')->assertSee('name="from" value="dashboard"', false)->assertSee('data-confirm="Qualifikationen erstellen?"', false);

        $this->post(route('surveys.store'), ['from' => 'dashboard'])->assertRedirect('/home');
        $this->assertSame(1, $camp->surveys()->count());
        $this->post(route('admin.camps.opensurvey'), ['from' => 'dashboard'])->assertRedirect('/home');
        $this->assertTrue($camp->fresh()->secondsurveyopen);
    }

    public function test_foreign_from_value_is_ignored(): void
    {
        $camp = $this->newCamp();

        $this->actingAs($camp->user)->post(route('admin.camps.opensurvey'), ['from' => 'https://example.com'])
            ->assertRedirect('/admin/surveys');
        $this->post(route('surveys.store'), ['from' => 'https://example.com'])->assertOk();
    }

    public function test_campleader_hides_and_shows_the_card_for_the_whole_camp(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->post(route('admin.camps.checklist'), ['hidden' => 1])->assertRedirect('/home');
        $this->assertTrue($s->camp->fresh()->checklist_hidden);
        $this->get('/home')->assertSee('Einblenden')->assertDontSee('Teilnehmende den Leitenden zuteilen');

        $this->post(route('admin.camps.checklist'), ['hidden' => 0]);
        $this->assertFalse($s->camp->fresh()->checklist_hidden);
        $this->get('/home')->assertSee('Ausblenden')->assertSee('Teilnehmende den Leitenden zuteilen');
    }

    public function test_leader_cannot_hide_the_card(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->post(route('admin.camps.checklist'), ['hidden' => 1]);

        $this->assertFalse($s->camp->fresh()->checklist_hidden);
    }

    /** Alle Besuchenden teilen sich den Demo-Kurs: ausblenden nur in der eigenen Sitzung. */
    public function test_demo_user_hides_the_card_only_in_the_session(): void
    {
        $camp = $this->newCamp(['demo' => true]);
        $demo = $camp->user;
        $demo->forceFill(['demo' => true])->save();

        $this->actingAs($demo)->post(route('admin.camps.checklist'), ['hidden' => 1])->assertRedirect('/home');

        $this->assertFalse($camp->fresh()->checklist_hidden);
        $this->get('/home')->assertSee('Einblenden');
    }

    public function test_campleader_creating_a_camp_lands_on_the_dashboard(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->post(route('admin.camps.store'), [
            'name' => 'Neuer Kurs', 'camp_type_id' => $s->camp->camp_type_id, 'group_id' => $s->group->id, 'end_date' => '2027-04-18',
        ])->assertRedirect('/home');

        $this->get('/home')->assertSee('Erste Schritte')->assertSee('Leitende und Teilnehmende importieren');
    }

    /** Kurs mit Kursleitung als einzigem Mitglied, wie direkt nach «Kurs erstellen». */
    private function newCamp(array $attributes = []): Camp
    {
        $group = Group::factory()->create();
        $kursleiter = User::factory()->kursleiter()->create(['group_id' => $group->id]);
        $camp = Camp::factory()->create(['user_id' => $kursleiter->id, 'group_id' => $group->id] + $attributes);
        $kursleiter->update(['camp_id' => $camp->id]);
        CampUser::create(['camp_id' => $camp->id, 'user_id' => $kursleiter->id, 'role_id' => Role::Kursleiter]);

        return $camp;
    }

    private function checklist(Camp $camp): array
    {
        return app(CampChecklist::class)->for($camp->fresh());
    }

    private function step(Camp $camp, string $key): ?array
    {
        return $this->checklist($camp)['steps']->firstWhere('key', $key);
    }

    private function next(Camp $camp): ?string
    {
        return $this->checklist($camp)['steps']->firstWhere('next', true)['key'] ?? null;
    }
}
