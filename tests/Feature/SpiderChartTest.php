<?php

namespace Tests\Feature;

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
        $leader = 'Leiter Qualifikation';

        return [
            'TN, 1. Runde' => ['participant', false, 'survey_1offen', [$first]],
            'TN, 2. Runde freigegeben' => ['participant', true, 'survey_2offen', [$first, $second]],
            'TN, Quali fertig' => ['participant', true, 'survey_fertig', [$first, $second]],
            'TN, 2. Runde freigegeben, selbst noch in 1. Runde' => ['participant', true, 'survey_1offen', [$first]],
            'Gruppenleiter, 1. Runde' => ['gruppenleiter', false, 'survey_1offen', [$first, $leader]],
            'Gruppenleiter, Status 2. offen ohne Freigabe' => ['gruppenleiter', false, 'survey_2offen', [$first, $leader]],
            'Gruppenleiter, 2. Runde freigegeben' => ['gruppenleiter', true, 'survey_2offen', [$first, $second, $leader]],
            'Kursleiter, 2. Runde freigegeben' => ['kursleiter', true, 'survey_tnAbgeschlossen', [$first, $second, $leader]],
        ];
    }

    #[DataProvider('visibleDatasets')]
    public function test_visible_datasets(string $role, bool $secondRoundReleased, string $status, array $expected)
    {
        $this->s->camp->update(['secondsurveyopen' => $secondRoundReleased]);
        $this->s->survey->update(['survey_status_id' => config('status.'.$status)]);
        $this->actingAs($this->s->{$role}->fresh());

        $datasets = Helper::GetSurveysDataset(collect([$this->s->survey->fresh()]))[0];

        $this->assertSame($expected, array_column($datasets, 'label'));
    }

    public function test_participant_never_receives_leader_assessment_on_overview()
    {
        $this->s->camp->update(['secondsurveyopen' => true]);
        $this->s->survey->update(['survey_status_id' => config('status.survey_fertig')]);

        $this->actingAs($this->s->participant->fresh())
            ->get('/home')
            ->assertOk()
            ->assertViewHas('datasets', function (array $datasets) {
                $labels = collect($datasets)->flatten(1)->pluck('label');

                return $labels->isNotEmpty() && ! $labels->contains('Leiter Qualifikation');
            });
    }

    public function test_quali_without_questions_does_not_break_pages()
    {
        // Eigener Kurstyp ohne Kapitel: "Qualis erstellen" legt dann Qualis ohne Fragen an.
        $participant = User::factory()->inCamp($this->s->camp, $this->s->gruppenleiter)->create();
        $empty = Survey::create([
            'camp_user_id' => $this->s->campUser($participant)->id,
            'slug' => (string) Str::uuid(),
            'survey_status_id' => config('status.survey_neu'),
        ]);
        $this->assertSame(0, SurveyChapter::where('survey_id', $empty->id)->count());

        $this->actingAs($this->s->gruppenleiter);
        $this->assertSame([], Helper::GetSurveysDataset(collect([$empty]))[0][0]['data']);

        $this->get('/home')->assertOk();
        $this->get(route('survey.compare', $empty))->assertOk();
    }
}
