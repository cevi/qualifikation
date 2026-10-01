<?php

namespace Tests\Feature\Security;

use App\Models\Camp;
use App\Models\Survey;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Kursleitende verwalten im Admin-Bereich nur ihren aktuellen Kurs. Kursleitende können sich selbst
 * registrieren und einen Kurs anlegen, deshalb darf die ID eines fremden Objekts nie genügen.
 */
class CrossCampManagementTest extends TestCase
{
    /**
     * Requests des Kursleiters von Kurs A auf Objekte von Kurs B.
     */
    public static function foreignRequests(): array
    {
        return [
            'Kurs bearbeiten' => ['get', static fn (CampScenario $b) => route('admin.camps.edit', $b->camp)],
            'Kurs ändern' => ['put', static fn (CampScenario $b) => route('admin.camps.update', $b->camp)],
            'Kurs abschliessen' => ['delete', static fn (CampScenario $b) => route('admin.camps.destroy', $b->camp)],
            'Person bearbeiten' => ['get', static fn (CampScenario $b) => route('admin.users.edit', $b->participant)],
            'Person ändern' => ['put', static fn (CampScenario $b) => route('admin.users.update', $b->participant->id)],
            'Person entfernen' => ['delete', static fn (CampScenario $b) => route('admin.users.destroy', $b->participant)],
            'Quali bearbeiten' => ['get', static fn (CampScenario $b) => route('surveys.edit', $b->survey)],
            'Quali ändern' => ['put', static fn (CampScenario $b) => route('surveys.update', $b->survey->id)],
            'Quali löschen' => ['delete', static fn (CampScenario $b) => route('surveys.destroy', $b->survey)],
            'Standardtext bearbeiten' => ['get', static fn (CampScenario $b) => route('admin.standard_texts.edit', $b->standardText)],
            'Standardtext ändern' => ['put', static fn (CampScenario $b) => route('admin.standard_texts.update', $b->standardText)],
            'Standardtext löschen' => ['delete', static fn (CampScenario $b) => route('admin.standard_texts.destroy', $b->standardText)],
        ];
    }

    #[DataProvider('foreignRequests')]
    public function test_kursleiter_cannot_manage_objects_of_foreign_camp(string $method, Closure $url)
    {
        $a = CampScenario::create();
        $b = CampScenario::create();

        $this->actingAs($a->kursleiter)
            ->{$method}($url($b), [
                'name' => 'Übernommen',
                'username' => 'Übernommen',
                'email' => 'uebernommen@example.com',
                'title' => 'Übernommen',
                'survey_status_id' => config('status.survey_fertig'),
            ])
            ->assertForbidden();
    }

    public function test_foreign_camp_data_stays_unchanged()
    {
        $a = CampScenario::create();
        $b = CampScenario::create();

        $this->actingAs($a->kursleiter);
        $this->put(route('admin.camps.update', $b->camp), ['name' => 'Übernommen']);
        $this->put(route('admin.users.update', $b->participant->id), ['username' => 'Übernommen', 'email' => 'uebernommen@example.com']);
        $this->put(route('surveys.update', $b->survey->id), ['survey_status_id' => config('status.survey_fertig')]);
        $this->put(route('admin.standard_texts.update', $b->standardText), ['title' => 'Übernommen', 'content' => 'x']);
        $this->delete(route('surveys.destroy', $b->survey));
        $this->delete(route('admin.camps.destroy', $b->camp));

        $this->assertNotSame('Übernommen', $b->camp->fresh()->name);
        $this->assertFalse($b->camp->fresh()->finish);
        $this->assertNotSame('Übernommen', $b->participant->fresh()->username);
        $this->assertSame($b->camp->id, $b->participant->fresh()->camp_id);
        $this->assertSame(config('status.survey_neu'), $b->survey->fresh()->survey_status_id);
        $this->assertNotSame('Übernommen', $b->standardText->fresh()->title);
    }

    public function test_kursleiter_manages_own_camp()
    {
        $a = CampScenario::create();

        $this->actingAs($a->kursleiter);
        $this->get(route('admin.camps.edit', $a->camp))->assertOk();
        $this->get(route('admin.users.edit', $a->participant))->assertOk();
        $this->get(route('surveys.edit', $a->survey))->assertOk();
        $this->put(route('admin.camps.update', $a->camp), ['name' => 'Neuer Name'])->assertRedirect('/admin/camps');
        $this->put(route('admin.users.update', $a->participant->id), ['username' => 'Neu', 'email' => 'neu@example.com'])->assertRedirect('/admin/users');
        $this->put(route('admin.standard_texts.update', $a->standardText), ['title' => 'Neu', 'content' => 'x'])->assertRedirect('/admin/standard_texts');

        $this->assertSame('Neuer Name', $a->camp->fresh()->name);
        $this->assertSame('Neu', $a->participant->fresh()->username);
        $this->assertSame('Neu', $a->standardText->fresh()->title);
    }

    public function test_kursleiter_cannot_make_anyone_admin()
    {
        $a = CampScenario::create();

        $this->actingAs($a->kursleiter)
            ->put(route('admin.users.update', $a->participant->id), [
                'username' => $a->participant->username,
                'email' => $a->participant->email,
                'role_id' => config('status.role_Administrator'),
            ]);

        $this->assertFalse($a->participant->fresh()->isAdmin());
    }

    public function test_kursleiter_cannot_change_admin_although_admin_is_in_every_camp()
    {
        $a = CampScenario::create();
        $email = $a->admin->email;

        $this->actingAs($a->kursleiter)
            ->put(route('admin.users.update', $a->admin->id), ['username' => 'Admin', 'email' => 'uebernommen@example.com', 'password' => 'uebernommen'])
            ->assertForbidden();

        $this->assertSame($email, $a->admin->fresh()->email);
    }

    public function test_kursleiter_cannot_set_password_of_camp_member()
    {
        $a = CampScenario::create();
        $hash = $a->participant->password;

        $this->actingAs($a->kursleiter)
            ->put(route('admin.users.update', $a->participant->id), [
                'username' => $a->participant->username,
                'email' => $a->participant->email,
                'password' => 'uebernommen',
            ]);

        $this->assertSame($hash, $a->participant->fresh()->password);
    }

    public function test_kursleiter_cannot_create_global_standard_text()
    {
        $a = CampScenario::create();

        $this->actingAs($a->kursleiter)
            ->post(route('admin.standard_texts.store'), ['title' => 'Für alle', 'content' => 'x', 'global' => true]);

        $this->assertDatabaseHas('standard_texts', ['title' => 'Für alle', 'camp_id' => $a->camp->id, 'global' => false]);
    }

    public function test_admin_finishing_a_camp_moves_its_members_to_global_camp()
    {
        $a = CampScenario::create();
        $global = Camp::where('global_camp', true)->firstOrFail();

        $this->actingAs($a->admin)
            ->delete(route('admin.camps.destroy', $a->camp));

        $this->assertTrue($a->camp->fresh()->finish);
        $this->assertSame($global->id, $a->participant->fresh()->camp_id);
        $this->assertSame($global->id, $a->kursleiter->fresh()->camp_id);
        $this->assertSame($global->id, $a->admin->fresh()->camp_id);
    }

    public function test_public_demo_kursleiter_cannot_delete_qualis()
    {
        $a = CampScenario::create();
        $demo = User::factory()->kursleiter()->demo()->inCamp($a->camp)->create();

        $this->actingAs($demo)->delete(route('surveys.destroy', $a->survey));

        $this->assertModelExists($a->survey);
    }
}
