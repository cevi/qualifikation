<?php

namespace Tests\Feature\Admin;

use App\Models\Answer;
use App\Models\Classification;
use App\Models\Feedback;
use App\Models\Help;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Globale Stammdaten, nur für Admins (Zugriff: Security/AdminOnlyAreasTest): anlegen, ändern, löschen.
 * Gefunden über die Abdeckung (Aktionen mit Route, die kein Test ausführte).
 */
class MasterDataTest extends TestCase
{
    private CampScenario $s;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
        $this->actingAs($this->s->admin);
    }

    public function test_admin_creates_changes_and_deletes_an_answer(): void
    {
        $this->post(route('answers.store'), ['name' => '+++', 'count' => 3, 'description' => 'Herausragend'])
            ->assertRedirect('admin/answers');
        $answer = Answer::where('name', '+++')->firstOrFail();

        $this->put(route('answers.update', $answer->id), ['name' => '+++', 'count' => 3, 'description' => 'Neu'])
            ->assertRedirect('/admin/answers');
        $this->assertSame('Neu', $answer->fresh()->description);

        $this->delete(route('answers.destroy', $answer->id))->assertRedirect('/admin/answers');
        $this->assertModelMissing($answer);
    }

    public function test_admin_renames_a_classification(): void
    {
        $classification = Classification::firstOrFail();

        $this->put(route('classifications.update', $classification->id), ['name' => 'Umbenannt'])
            ->assertRedirect('/admin/classifications');

        $this->assertSame('Umbenannt', $classification->fresh()->name);
    }

    public function test_admin_creates_and_deletes_a_help_article(): void
    {
        $this->post(route('helps.store'), ['title' => 'Wartung', 'content' => 'So geht es'])->assertRedirect('admin/helps');
        $help = Help::where('title', 'Wartung')->firstOrFail();

        $this->delete(route('helps.destroy', $help))->assertRedirect('admin/helps');
        $this->assertModelMissing($help);
    }

    public function test_admin_changes_a_group_and_stores_the_token_encrypted(): void
    {
        $this->put(route('groups.update', $this->s->group), ['name' => 'Cevi Neu', 'shortname' => 'neu', 'api_token' => 'geheim'])
            ->assertRedirect('/admin/groups');

        $group = $this->s->group->fresh();
        $this->assertSame('Cevi Neu', $group->name);
        $this->assertNotSame('geheim', $group->api_token);
        $this->assertSame('geheim', Crypt::decryptString($group->api_token));
    }

    public function test_admin_edits_a_feedback(): void
    {
        $this->put(route('feedback.update', $this->s->feedback), ['feedback' => 'Bereinigt'])->assertRedirect('/admin/feedback');

        $this->assertSame('Bereinigt', Feedback::findOrFail($this->s->feedback->id)->feedback);
    }

    /** Leere Controller-Methoden ohne Route (vorher 200 mit leerer Seite bzw. stilles Nichtstun). */
    public function test_empty_actions_have_no_routes(): void
    {
        foreach (['classifications.create', 'classifications.store', 'classifications.show', 'classifications.destroy', 'posts.create', 'posts.show', 'posts.update'] as $name) {
            $this->assertFalse(Route::has($name), $name);
        }
        $this->assertTrue(Route::has('classifications.update'));
        $this->assertTrue(Route::has('posts.store'));
    }

    public function test_failed_github_issue_is_reported_as_error(): void
    {
        config(['auth.github.api_url' => 'http://127.0.0.1:9']);

        $this->post('/users/feedback/send', ['title' => 'Fehler', 'description' => 'Beschreibung'])
            ->assertRedirect('admin/feedback')
            ->assertSessionMissing('success')
            ->assertSessionHasErrors('github');
    }
}
