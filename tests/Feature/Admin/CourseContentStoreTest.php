<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\AdminQuestionsController;
use App\Models\CampType;
use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Support\Facades\Route;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Kapitel und Kompetenzen anlegen. Ändern und fremde Kurstypen: Security/CampTypeContentTest.
 */
class CourseContentStoreTest extends TestCase
{
    private CampScenario $s;

    private CampType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s = CampScenario::create();
        $this->type = CampType::create(['name' => 'Eigener Typ', 'user_id' => $this->s->kursleiter->id]);
        $this->s->camp->update(['camp_type_id' => $this->type->id]);
        $this->actingAs($this->s->kursleiter->fresh());
    }

    public function test_campleader_adds_a_chapter_to_the_own_camp_type(): void
    {
        $foreign = CampType::create(['name' => 'Fremd', 'user_id' => $this->s->admin->id]);

        $this->post(route('chapters.store'), ['number' => 5, 'name' => 'Eigenes Kapitel', 'camp_type_id' => $foreign->id])
            ->assertRedirect('admin/chapters');

        // Der Kurstyp kommt aus dem aktuellen Kurs, nicht aus der Anfrage
        $this->assertSame($this->type->id, Chapter::where('name', 'Eigenes Kapitel')->firstOrFail()->camp_type_id);
    }

    public function test_chapter_needs_number_and_name(): void
    {
        $this->post(route('chapters.store'), ['name' => ''])
            ->assertRedirect('admin/chapters')
            ->assertSessionHasErrors(['number', 'name']);

        $this->assertSame(0, Chapter::where('camp_type_id', $this->type->id)->count());
    }

    public function test_campleader_adds_a_competence_to_an_own_chapter_only(): void
    {
        $own = Chapter::create(['number' => 1, 'name' => 'Eigenes', 'camp_type_id' => $this->type->id]);
        $default = Chapter::whereNull('camp_type_id')->orWhereHas('camp_type', fn ($query) => $query->where('default_type', true))->firstOrFail();
        $data = ['number' => '1.9', 'competence' => 'Kompetenz', 'sort-index' => 9];

        $this->post(route('questions.store'), $data + ['name' => 'Eigene Frage', 'chapter_id' => $own->id])->assertRedirect('admin/questions');
        $this->post(route('questions.store'), $data + ['name' => 'Fremde Frage', 'chapter_id' => $default->id])->assertForbidden();

        $this->assertSame($this->type->id, Question::where('name', 'Eigene Frage')->firstOrFail()->camp_type_id);
        $this->assertFalse(Question::where('name', 'Fremde Frage')->exists());
    }

    /** Der CSV-Import hatte kein Formular mehr, keine Berechtigungsprüfung und legte die Datei öffentlich ab. */
    public function test_csv_import_of_competences_is_gone(): void
    {
        $this->assertFalse(collect(Route::getRoutes())->contains(fn ($route) => $route->uri() === 'admin/questions/uploadFile'));
        $this->assertFalse(method_exists(AdminQuestionsController::class, 'uploadFile'));
    }
}
