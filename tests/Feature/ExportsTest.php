<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Exports\PostsExport;
use App\Models\Post;
use Illuminate\Support\Str;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Excel-Export der Rückmeldungen und Druckansichten (PDF) der Qualis.
 */
class ExportsTest extends TestCase
{
    public function test_kursleiter_downloads_feedback_as_excel()
    {
        $s = CampScenario::create();

        $response = $this->actingAs($s->kursleiter)
            ->get(route('admin.camps.export'))
            ->assertOk()
            ->assertDownload();

        $this->assertStringContainsString('_'.Str::slug($s->camp->name).'.xlsx', $response->headers->get('content-disposition'));
    }

    /** Kursübersicht: Export und Druck gelten für den Kurs der Zeile, nicht für den aktuellen Kurs. */
    public function test_admin_exports_and_prints_the_camp_of_the_row()
    {
        $s = CampScenario::create();
        $other = CampScenario::create();
        $other->participant->update(['username' => 'Nur im anderen Kurs']);
        $this->actingAs($s->admin);

        $row = collect($this->getJson(route('camps.table'))->json('data'))->firstWhere('id', $other->camp->id);
        $urls = array_column($row['actions'], 'url', 'label');
        $this->assertSame(route('admin.camps.export', $other->camp), $urls['Rückmeldungen exportieren']);
        $this->assertSame(route('surveys.downloadPDF', $other->camp), $urls['Qualifikationen drucken']);

        $response = $this->get($urls['Rückmeldungen exportieren'])->assertOk()->assertDownload();
        $this->assertStringContainsString('_'.Str::slug($other->camp->name).'.xlsx', $response->headers->get('content-disposition'));
        $this->get($urls['Qualifikationen drucken'])->assertOk()->assertSee('Nur im anderen Kurs');

        $html = (new PostsExport($other->camp))->view()->render();
        $this->assertStringContainsString(e($other->post->comment), $html);
        $this->assertStringNotContainsString(e($s->post->comment), $html);
    }

    public function test_excel_contains_own_camp_feedback_including_general_feedback()
    {
        $s = CampScenario::create();
        $other = CampScenario::create();
        Post::factory()->for($s->gruppenleiter, 'leader')->create(['camp_id' => $s->camp->id, 'comment' => 'Allgemeine Rückmeldung']);

        $this->actingAs($s->kursleiter);
        $html = (new PostsExport)->view()->render();

        $this->assertStringContainsString(e($s->post->comment), $html);
        $this->assertStringContainsString('Allgemeine Rückmeldung', $html);
        $this->assertStringNotContainsString(e($other->post->comment), $html);
    }

    public function test_leaders_open_print_view_of_quali()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->get(route('survey.downloadPDF', $s->survey))->assertOk();
        $this->actingAs($s->kursleiter)->get(route('survey.downloadPDF', $s->survey))->assertOk();
        $this->actingAs($s->kursleiter)->get(route('surveys.downloadPDF'))->assertOk();
    }

    public function test_participant_cannot_open_print_view_with_leader_assessment()
    {
        // Die Druckansicht enthält die Leiter-Einschätzung; die Vergleichsseite blendet sie für TN aus.
        $s = CampScenario::create();
        $s->survey->update(['survey_status_id' => SurveyStatus::Fertig]);

        $this->actingAs($s->participant)->get(route('survey.downloadPDF', $s->survey))->assertForbidden();
    }

    public function test_leader_of_other_camp_cannot_open_print_view()
    {
        $s = CampScenario::create();
        $other = CampScenario::create();

        $this->actingAs($other->kursleiter)->get(route('survey.downloadPDF', $s->survey))->assertForbidden();
        $this->actingAs($s->gruppenleiter)->get(route('survey.downloadPDF', $s->unassignedSurvey))->assertForbidden();
    }
}
