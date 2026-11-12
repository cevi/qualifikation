<?php

namespace Tests\Feature;

use App\Enums\SurveyStatus;
use App\Helper\Helper;
use App\Models\Answer;
use App\Models\Competence;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyChapter;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Spider-Graphen: Helper::GetSurveysLabels() und Helper::GetSurveysDataset().
 */
class SpiderChartTest extends TestCase
{
    private CampScenario $s;

    protected function setUp(): void
    {
        parent::setUp();

        $this->s = CampScenario::create();
        // Sortierung umgekehrt zur ID-Reihenfolge, damit die Tests sie wirklich prüfen.
        foreach (Question::all() as $question) {
            $question->update(['sort-index' => 100 - $question->id]);
        }
    }

    public function test_labels_follow_sort_index_and_mark_core_competences()
    {
        $this->actingAs($this->s->gruppenleiter);
        $withCompetence = Competence::where('camp_type_id', $this->s->camp->camp_type_id)->pluck('question_id');

        $expected = Question::orderBy('sort-index')->get()
            ->map(fn (Question $q) => ($withCompetence->contains($q->id) ? '*' : '').$q->number.' '.$q->competence)
            ->all();

        $this->assertNotEmpty($withCompetence);
        $this->assertSame($expected, Helper::GetSurveysLabels(collect([$this->s->survey])));
    }

    public function test_values_and_ids_follow_label_order()
    {
        $this->actingAs($this->s->gruppenleiter);
        $counts = [-1, 0, 1, 2];
        foreach ($this->s->survey->questions()->get() as $i => $surveyQuestion) {
            SurveyQuestion::findOrFail($surveyQuestion->id)->update([
                'answer_first_id' => Answer::where('count', $counts[$i % 4])->value('id'),
                'answer_leader_id' => Answer::where('count', $counts[($i + 1) % 4])->value('id'),
            ]);
        }

        [$first, $leader] = Helper::GetSurveysDataset(collect([$this->s->survey]))[0];

        $ordered = SurveyQuestion::whereIn('id', $this->s->survey->questions()->pluck('survey_questions.id'))
            ->with(['question', 'answer_first', 'answer_leader'])->get()
            ->sortBy('question.sort-index')->values();
        $this->assertSame($ordered->pluck('question.id')->all(), $first['dataset_ids']);
        $this->assertSame($ordered->pluck('answer_first.count')->all(), $first['data']);
        $this->assertSame($ordered->pluck('answer_leader.count')->all(), $leader['data']);
    }

    /**
     * Welche Datensätze sieht wer? [Rolle, 2. Runde freigegeben, Status der Quali, sichtbare Datensätze]
     */
    public static function visibleDatasets(): array
    {
        $first = '1. Selbsteinschätzung';
        $second = '2. Selbsteinschätzung';
        $leader = 'Einschätzung Leitende';

        return [
            'TN, 1. Runde' => ['participant', false, SurveyStatus::ErsteRundeOffen, [$first]],
            'TN, 2. Runde freigegeben' => ['participant', true, SurveyStatus::ZweiteRundeOffen, [$first, $second]],
            'TN, Quali fertig' => ['participant', true, SurveyStatus::Fertig, [$first, $second]],
            'TN, 2. Runde freigegeben, selbst noch in 1. Runde' => ['participant', true, SurveyStatus::ErsteRundeOffen, [$first]],
            'Gruppenleiter, 1. Runde' => ['gruppenleiter', false, SurveyStatus::ErsteRundeOffen, [$first, $leader]],
            'Gruppenleiter, Status 2. offen ohne Freigabe' => ['gruppenleiter', false, SurveyStatus::ZweiteRundeOffen, [$first, $leader]],
            'Gruppenleiter, 2. Runde freigegeben' => ['gruppenleiter', true, SurveyStatus::ZweiteRundeOffen, [$first, $second, $leader]],
            'Kursleiter, 2. Runde freigegeben' => ['kursleiter', true, SurveyStatus::TnAbgeschlossen, [$first, $second, $leader]],
        ];
    }

    #[DataProvider('visibleDatasets')]
    public function test_visible_datasets(string $role, bool $secondRoundReleased, SurveyStatus $status, array $expected)
    {
        $this->s->camp->update(['secondsurveyopen' => $secondRoundReleased]);
        $this->s->survey->update(['survey_status_id' => $status]);
        $this->actingAs($this->s->{$role}->fresh());

        $datasets = Helper::GetSurveysDataset(collect([$this->s->survey->fresh()]))[0];

        $this->assertSame($expected, array_column($datasets, 'label'));
    }

    public function test_participant_never_receives_leader_assessment_on_overview()
    {
        $this->s->camp->update(['secondsurveyopen' => true]);
        $this->s->survey->update(['survey_status_id' => SurveyStatus::Fertig]);

        // Seit Oktober 2026 zeigt die Übersicht gar kein Diagramm mehr (nur Karten), also auch keine Leitungsdaten.
        $this->actingAs($this->s->participant->fresh())
            ->get('/home')
            ->assertOk()
            ->assertViewMissing('datasets')
            ->assertDontSee('Einschätzung Leitende');
    }

    public function test_quali_without_questions_does_not_break_pages()
    {
        // Eigener Kurstyp ohne Kapitel: "Qualis erstellen" legt dann Qualis ohne Fragen an.
        $participant = User::factory()->inCamp($this->s->camp, $this->s->gruppenleiter)->create();
        $empty = Survey::create([
            'camp_user_id' => $this->s->campUser($participant)->id,
            'slug' => (string) Str::uuid(),
            'survey_status_id' => SurveyStatus::Neu,
        ]);
        $this->assertSame(0, SurveyChapter::where('survey_id', $empty->id)->count());

        $this->actingAs($this->s->gruppenleiter);
        $this->assertSame([], Helper::GetSurveysDataset(collect([$empty]))[0][0]['data']);

        $this->get('/home')->assertOk();
        $this->get(route('survey.compare', $empty))->assertOk();
    }

    public function test_rounds_are_told_apart_by_color_and_line(): void
    {
        $this->s->camp->update(['secondsurveyopen' => true]);
        $this->s->survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
        $this->actingAs($this->s->gruppenleiter);

        $datasets = collect(Helper::GetSurveysDataset(collect([$this->s->survey->fresh()]))[0])->keyBy('series');

        $this->assertSame(['first', 'second', 'leader'], $datasets->keys()->all());
        $this->assertSame([6, 4], $datasets['first']['borderDash'], '1. Runde gestrichelt');
        $this->assertSame([], $datasets['second']['borderDash']);
        $this->assertSame('#1a56db', $datasets['second']['borderColor']);
        $this->assertSame('#d03801', $datasets['leader']['borderColor']);
        // Linien deckend, nicht mehr halb durchsichtig wie die alten rgba(…, 0.2)
        $datasets->each(fn (array $dataset) => $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $dataset['borderColor']));
    }
}
