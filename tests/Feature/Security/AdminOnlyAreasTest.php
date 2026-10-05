<?php

namespace Tests\Feature\Security;

use App\Enums\Classification;
use App\Mail\FeedbackCreated;
use App\Models\Answer;
use App\Models\Classification as ClassificationModel;
use App\Models\Feedback;
use App\Models\Group;
use App\Models\Help;
use Closure;
use Database\Seeders\DemoCampSeeder;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Globale Stammdaten (Gruppen, Antworten, Klassifizierungen, Hilfe, Feedback) verwalten nur Admins.
 * Kursleitende können sich selbst registrieren und einen Kurs anlegen, sind also nicht vertrauenswürdig.
 */
class AdminOnlyAreasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // FeedbackController@send legt GitHub-Issues an. Falls der Schutz versagt, darf nichts rausgehen.
        config(['auth.github.api_url' => 'http://127.0.0.1:9']);
    }

    public static function adminOnlyRequests(): array
    {
        return [
            'Gruppen ansehen' => ['get', static fn (CampScenario $s) => route('groups.index')],
            'Gruppe bearbeiten' => ['get', static fn (CampScenario $s) => route('groups.edit', $s->group)],
            'Gruppe anlegen' => ['post', static fn (CampScenario $s) => route('groups.store')],
            'Gruppe ändern' => ['put', static fn (CampScenario $s) => route('groups.update', $s->group)],
            'Gruppe löschen' => ['delete', static fn (CampScenario $s) => route('groups.destroy', $s->group)],
            'Antworten ansehen' => ['get', static fn (CampScenario $s) => route('answers.index')],
            'Antwort ändern' => ['put', static fn (CampScenario $s) => route('answers.update', 1)],
            'Antwort löschen' => ['delete', static fn (CampScenario $s) => route('answers.destroy', 1)],
            'Klassifizierungen ansehen' => ['get', static fn (CampScenario $s) => route('classifications.index')],
            'Klassifizierung ändern' => ['put', static fn (CampScenario $s) => route('classifications.update', 1)],
            'Klassifizierung löschen' => ['delete', static fn (CampScenario $s) => route('classifications.destroy', Classification::Rot)],
            'Hilfe ansehen' => ['get', static fn (CampScenario $s) => route('helps.index')],
            'Hilfe ändern' => ['put', static fn (CampScenario $s) => route('helps.update', $s->help)],
            'Hilfe löschen' => ['delete', static fn (CampScenario $s) => route('helps.destroy', $s->help)],
            'Feedback ansehen' => ['get', static fn (CampScenario $s) => route('feedback.index')],
            'Feedback löschen' => ['delete', static fn (CampScenario $s) => route('feedback.destroy', $s->feedback)],
            'Feedback an GitHub senden' => ['post', static fn (CampScenario $s) => '/users/feedback/send'],
        ];
    }

    #[DataProvider('adminOnlyRequests')]
    public function test_kursleiter_is_redirected_from_admin_only_area(string $method, Closure $url)
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->{$method}($url($s), ['name' => 'Geändert', 'title' => 'Geändert', 'description' => 'x'])
            ->assertRedirect('/home');
    }

    public function test_kursleiter_cannot_change_or_delete_global_data()
    {
        $s = CampScenario::create();
        $answer = Answer::create(['name' => '?', 'count' => 9, 'description' => 'Unbenutzt']);
        $classification = ClassificationModel::create(['name' => 'Unbenutzt']);

        $this->actingAs($s->kursleiter);
        $this->put(route('helps.update', $s->help), ['title' => 'Geändert', 'content' => 'Geändert']);
        $this->delete(route('groups.destroy', $s->group));
        $this->delete(route('answers.destroy', $answer->id));
        $this->delete(route('classifications.destroy', $classification->id));
        $this->delete(route('feedback.destroy', $s->feedback));

        $this->assertSame('Profil', $s->help->fresh()->title);
        $this->assertModelExists($s->group);
        $this->assertModelExists($answer);
        $this->assertModelExists($classification);
        $this->assertModelExists($s->feedback);
    }

    public function test_public_demo_kursleiter_cannot_delete_global_data()
    {
        $this->seed(DemoCampSeeder::class);
        $help = Help::create(['title' => 'Profil', 'content' => 'Hilfe']);
        $group = Group::factory()->create();

        $this->get('/loginKursleiter');
        $this->delete(route('helps.destroy', $help));
        $this->delete(route('groups.destroy', $group));

        $this->assertModelExists($help);
        $this->assertModelExists($group);
    }

    public function test_kursleiter_can_still_submit_feedback()
    {
        $s = CampScenario::create();
        Mail::fake();

        $this->actingAs($s->kursleiter)
            ->post(route('feedback.store'), ['feedback' => 'Bitte mehr Farben'])
            ->assertRedirect('admin/changes');

        $this->assertDatabaseHas('feedback', ['user_id' => $s->kursleiter->id, 'feedback' => 'Bitte mehr Farben']);
        Mail::assertSent(FeedbackCreated::class, fn (FeedbackCreated $mail) => $mail->hasTo(config('mail.camp.address')));
    }

    public function test_admin_can_manage_global_data()
    {
        $s = CampScenario::create();

        $this->actingAs($s->admin)
            ->put(route('helps.update', $s->help), ['title' => 'Geändert', 'content' => 'Neu'])
            ->assertRedirect('admin/helps');
        $this->actingAs($s->admin)
            ->delete(route('feedback.destroy', $s->feedback))
            ->assertRedirect('/admin/feedback');

        $this->assertSame('Geändert', $s->help->fresh()->title);
        $this->assertModelMissing($s->feedback);
    }
}
