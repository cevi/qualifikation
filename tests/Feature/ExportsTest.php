<?php

namespace Tests\Feature;

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
        $s->survey->update(['survey_status_id' => config('status.survey_fertig')]);

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
