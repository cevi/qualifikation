<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Mail\CampCreated;
use App\Models\Camp;
use App\Models\CampType;
use App\Models\CampUser;
use Illuminate\Support\Facades\Mail;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Kurse und Kurstypen anlegen: "Kurs erstellen" für alle (home), "Kurs erstellen" im Kursadmin, eigener Kurstyp.
 */
class CampCreationTest extends TestCase
{
    public function test_leader_creates_a_camp_and_becomes_its_campleader(): void
    {
        Mail::fake();
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->post(route('home.camps.store'), [
            'name' => 'Mein Kurs', 'camp_type_id' => $s->camp->camp_type_id, 'group_id' => $s->group->id,
            'end_date' => '2026-12-01', 'status_control' => 'on',
        ])->assertRedirect('home');

        $camp = Camp::where('name', 'Mein Kurs')->firstOrFail();
        $leader = $s->gruppenleiter->fresh();
        $this->assertSame($leader->id, $camp->user_id);
        $this->assertFalse($camp->global_camp);
        $this->assertSame(SurveyStatus::Neu, $camp->survey_status_id);
        $this->assertSame($camp->id, $leader->camp_id);
        $this->assertSame(Role::Kursleiter, $leader->role_id);
        $this->assertTrue(CampUser::where('camp_id', $camp->id)->where('user_id', $leader->id)->where('role_id', Role::Kursleiter)->exists());
        Mail::assertSent(CampCreated::class, fn (CampCreated $mail) => $mail->build()->hasTo($leader->email));
    }

    public function test_camp_name_must_be_unique(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->from(route('home.camps.create'))
            ->post(route('home.camps.store'), ['name' => $s->camp->name])
            ->assertRedirect(route('home.camps.create'))
            ->assertSessionHasErrors('name', null, 'camps');

        $this->assertSame(1, Camp::where('name', $s->camp->name)->count());
    }

    public function test_admin_creates_a_camp_for_a_campleader_in_course_admin(): void
    {
        Mail::fake();
        $s = CampScenario::create();

        $this->actingAs($s->admin)->post(route('admin.camps.store'), [
            'name' => 'Vom Admin', 'user_id' => $s->kursleiter->id, 'camp_type_id' => $s->camp->camp_type_id,
            'group_id' => $s->group->id, 'end_date' => '2026-12-01',
        ])->assertRedirect('admin/camps');

        $camp = Camp::where('name', 'Vom Admin')->firstOrFail();
        $this->assertSame($s->kursleiter->id, $camp->user_id);
        $this->assertFalse($camp->status_control);
    }

    public function test_leader_creates_an_own_camp_type_in_both_places(): void
    {
        $s = CampScenario::create();
        $this->actingAs($s->kursleiter);

        $this->post(route('camp_types.store'), ['name' => 'Typ A', 'user_id' => $s->admin->id])->assertRedirect(route('home.camps.create'));
        $this->post(route('admin.camp_types.store'), ['name' => 'Typ B'])->assertRedirect(route('home.camps.create'));

        // Besitzer ist immer die angemeldete Person, auch wenn eine andere mitgeschickt wird
        $this->assertSame([$s->kursleiter->id, $s->kursleiter->id], CampType::whereIn('name', ['Typ A', 'Typ B'])->pluck('user_id')->all());
    }

    /** Kursleitende legen im Kursadmin nur einen normalen, offenen Kurs an und werden dessen Mitglied. */
    public function test_campleader_cannot_set_protected_fields_and_becomes_member(): void
    {
        Mail::fake();
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->post(route('admin.camps.store'), [
            'name' => 'Eigener Kurs', 'camp_type_id' => $s->camp->camp_type_id, 'group_id' => $s->group->id, 'end_date' => '2026-12-01',
            'global_camp' => 1, 'finish' => 1, 'secondsurveyopen' => 1, 'counter' => 99, 'user_id' => $s->admin->id,
        ])->assertRedirect('/home'); // dort wartet «Erste Schritte» (CampChecklistTest)

        $camp = Camp::where('name', 'Eigener Kurs')->firstOrFail();
        $this->assertFalse($camp->global_camp);
        $this->assertFalse($camp->finish);
        $this->assertFalse($camp->secondsurveyopen);
        $this->assertNotSame(99, $camp->counter);
        $this->assertSame($s->kursleiter->id, $camp->user_id);
        $this->assertSame($camp->id, $s->kursleiter->fresh()->camp_id);
        $this->assertTrue(CampUser::where('camp_id', $camp->id)->where('user_id', $s->kursleiter->id)->where('role_id', Role::Kursleiter)->exists());
    }
}
