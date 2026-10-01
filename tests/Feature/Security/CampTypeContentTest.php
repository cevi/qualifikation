<?php

namespace Tests\Feature\Security;

use App\Models\CampType;
use App\Models\Chapter;
use App\Models\Competence;
use App\Models\Question;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Kurstypen mit ihren Kapiteln, Fragen und Kompetenzen. Kursleitende bearbeiten nur den eigenen Kurstyp
 * ihres aktuellen Kurses; Standard-Kurstypen und Standardkapitel gehören allen Kursen und damit den Admins.
 */
class CampTypeContentTest extends TestCase
{
    private CampScenario $a;

    private CampScenario $b;

    /** @var array<string, CampType|Chapter|Question|Competence> */
    private array $own;

    /** @var array<string, CampType|Chapter|Question|Competence> */
    private array $foreign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->a = CampScenario::create();
        $this->b = CampScenario::create();
        $this->own = $this->customCampTypeFor($this->a);
        $this->foreign = $this->customCampTypeFor($this->b);
    }

    public function test_kursleiter_can_change_content_of_own_camp_type()
    {
        $this->actingAs($this->a->kursleiter);
        $this->put(route('admin.camp_types.update', $this->own['type']), ['name' => 'Neu'])->assertRedirect('/admin/camp_types');
        $this->put(route('chapters.update', $this->own['chapter']->id), ['name' => 'Neu', 'number' => 1])->assertRedirect('/admin/chapters');
        $this->put(route('questions.update', $this->own['question']->id), $this->questionData($this->own['chapter']))->assertRedirect('/admin/questions');
        $this->put(route('competences.update', $this->own['competence']), $this->competenceData($this->own))->assertRedirect('/admin/competences');

        $this->assertSame('Neu', $this->own['type']->fresh()->name);
        $this->assertSame('Neu', $this->own['chapter']->fresh()->name);
        $this->assertSame('Neu', $this->own['question']->fresh()->name);
        $this->assertSame('Neu', $this->own['competence']->fresh()->name);
    }

    public function test_kursleiter_cannot_change_content_of_foreign_camp_type()
    {
        $this->actingAs($this->a->kursleiter);
        $this->get(route('admin.camp_types.edit', $this->foreign['type']))->assertForbidden();
        $this->put(route('admin.camp_types.update', $this->foreign['type']), ['name' => 'Übernommen'])->assertForbidden();
        $this->get(route('chapters.edit', $this->foreign['chapter']->id))->assertForbidden();
        $this->put(route('chapters.update', $this->foreign['chapter']->id), ['name' => 'Übernommen', 'number' => 1])->assertForbidden();
        $this->get(route('questions.edit', $this->foreign['question']->id))->assertForbidden();
        $this->put(route('questions.update', $this->foreign['question']->id), $this->questionData($this->foreign['chapter'], 'Übernommen'))->assertForbidden();
        $this->get(route('competences.edit', $this->foreign['competence']))->assertForbidden();
        $this->put(route('competences.update', $this->foreign['competence']), $this->competenceData($this->foreign, 'Übernommen'))->assertForbidden();

        $this->delete(route('competences.destroy', $this->foreign['competence']))->assertForbidden();
        $this->delete(route('questions.destroy', $this->foreign['question']->id))->assertForbidden();
        $this->delete(route('chapters.destroy', $this->foreign['chapter']->id))->assertForbidden();
        $this->delete(route('admin.camp_types.destroy', $this->foreign['type']))->assertForbidden();

        foreach ($this->foreign as $model) {
            $this->assertModelExists($model);
            $this->assertNotSame('Übernommen', $model->fresh()->name);
        }
    }

    public function test_kursleiter_cannot_change_default_content()
    {
        $defaultType = CampType::findOrFail(config('status.camptype_JS1'));
        $defaultType->forceFill(['default_type' => true])->save();
        $defaultChapter = Chapter::whereNull('camp_type_id')->firstOrFail();
        $defaultQuestion = Question::where('chapter_id', $defaultChapter->id)->firstOrFail();
        $defaultCompetence = Competence::where('camp_type_id', $defaultType->id)->firstOrFail();
        // Auch wenn der aktuelle Kurs den Standard-Kurstyp verwendet.
        $this->a->camp->update(['camp_type_id' => $defaultType->id]);

        $this->actingAs($this->a->kursleiter->fresh());
        $this->put(route('admin.camp_types.update', $defaultType), ['name' => 'Übernommen'])->assertForbidden();
        $this->put(route('chapters.update', $defaultChapter->id), ['name' => 'Übernommen', 'number' => 1])->assertForbidden();
        $this->put(route('questions.update', $defaultQuestion->id), $this->questionData($defaultChapter, 'Übernommen'))->assertForbidden();
        $this->delete(route('competences.destroy', $defaultCompetence))->assertForbidden();

        $this->assertModelExists($defaultCompetence);
        $this->assertNotSame('Übernommen', $defaultChapter->fresh()->name);
    }

    public function test_kursleiter_cannot_create_content_for_foreign_camp_type()
    {
        $this->actingAs($this->a->kursleiter)->post(route('competences.store'), [
            'name' => 'Untergeschoben',
            'camp_type_id' => $this->foreign['type']->id,
            'question_id' => $this->foreign['question']->id,
        ]);

        $this->assertDatabaseMissing('competences', ['name' => 'Untergeschoben', 'camp_type_id' => $this->foreign['type']->id]);
    }

    public function test_kursleiter_cannot_move_question_into_foreign_chapter()
    {
        $this->actingAs($this->a->kursleiter)
            ->put(route('questions.update', $this->own['question']->id), $this->questionData($this->foreign['chapter']));

        $this->assertSame($this->own['chapter']->id, $this->own['question']->fresh()->chapter_id);
    }

    public function test_admin_can_change_default_content()
    {
        $defaultChapter = Chapter::whereNull('camp_type_id')->firstOrFail();

        $this->actingAs($this->a->admin)
            ->put(route('chapters.update', $defaultChapter->id), ['name' => 'Neu', 'number' => 1])
            ->assertRedirect('/admin/chapters');

        $this->assertSame('Neu', $defaultChapter->fresh()->name);
    }

    /**
     * Eigener Kurstyp (wie über "Kurs-Typ erstellen" angelegt) samt Kapitel, Frage und Kompetenz, als Kurstyp des Kurses.
     */
    private function customCampTypeFor(CampScenario $s): array
    {
        $type = CampType::create(['name' => 'Typ von '.$s->kursleiter->username, 'user_id' => $s->kursleiter->id]);
        $chapter = Chapter::create(['number' => 1, 'name' => 'Kapitel', 'camp_type_id' => $type->id]);
        $question = Question::create([
            'name' => 'Frage', 'number' => '1.1', 'competence' => 'Kompetenz', 'sort-index' => 1,
            'chapter_id' => $chapter->id, 'camp_type_id' => $type->id,
        ]);
        $competence = Competence::create(['name' => 'Kernkompetenz', 'camp_type_id' => $type->id, 'question_id' => $question->id]);
        $s->camp->update(['camp_type_id' => $type->id]);

        return compact('type', 'chapter', 'question', 'competence');
    }

    private function questionData(Chapter $chapter, string $name = 'Neu'): array
    {
        return ['name' => $name, 'number' => '1.1', 'competence' => 'Kompetenz', 'sort-index' => 1, 'chapter_id' => $chapter->id];
    }

    private function competenceData(array $type, string $name = 'Neu'): array
    {
        return ['name' => $name, 'camp_type_id' => $type['type']->id, 'question_id' => $type['question']->id];
    }
}
