<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Helper\Helper;
use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Help;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyChapter;
use App\Models\SurveyQuestion;
use App\Models\SurveyStatus as SurveyStatusModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSurveysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $camp = Auth::user()->camp;
        $title = 'Qualifikationen';

        $help = Help::where('title', $title)->first();
        $hasSurveys = $camp->surveys()->exists();

        return view('admin.surveys.index', compact('camp', 'title', 'help', 'hasSurveys'));
    }

    /**
     * Zeilen der Quali-Liste (admin/surveys/index, Vue-Tabelle): nur Text und URLs, Links und Status baut die Tabelle.
     */
    public function tableData()
    {
        if (! Auth::user()->isAdmin()) {
            $surveys = Auth::user()->camp->surveys;
        } else {
            $surveys = Survey::all();
        }
        $surveys->load(['campuser.user.group', 'campuser.leader', 'campuser.camp']);
        $names = SurveyStatusModel::pluck('name', 'id');

        return response()->json(['data' => $surveys->map(function (Survey $survey) use ($names) {
            $campUser = $survey->campuser;
            // Ohne zweite Runde fehlt der Schritt "2. offen".
            $steps = array_filter(SurveyStatus::cases(), fn (SurveyStatus $status) => $campUser->camp['secondsurveyopen'] || $status !== SurveyStatus::ZweiteRundeOffen);

            return [
                'id' => $survey->id,
                'name' => $campUser->user->username,
                'profile_url' => route('home.profile', $campUser->user->slug),
                'leader' => $campUser->leader?->username ?? '',
                'camp' => $campUser->camp->name,
                'group' => $campUser->user->group?->shortname ?? '',
                'steps' => array_map(fn (SurveyStatus $status) => [
                    'name' => $names[$status->value],
                    'done' => $survey->status()->isAtLeast($status),
                ], array_values($steps)),
                'actions' => [
                    ['label' => 'Zur Qualifikation', 'url' => route('survey.compare', $survey->slug)],
                    ['label' => 'Bearbeiten', 'url' => route('surveys.edit', $survey->slug)],
                ],
            ];
        })->values()]);
    }

    /**
     * Erstellt die Qualifikationen für alle Teilnehmenden des aktuellen Kurses, die noch keine haben.
     *
     * @return Response
     */
    public function store()
    {
        $camp = Auth::user()->camp;

        // Das Global-Camp hat keinen Kurstyp und damit keine Kapitel.
        if (! Auth::user()->demo && $camp->camp_type) {
            $camp_users = $camp->camp_users()->doesntHave('surveys')->get();
            $camp_type = $camp->camp_type;
            $answer = Answer::where('name', '0')->first();
            if ($camp_type['default_type']) {
                $chapters = Chapter::where('default_chapter', true)->get();
            } else {
                $chapters = Chapter::where('camp_type_id', $camp_type['id'])->get();
            }

            foreach ($camp_users as $camp_user) {
                $input['name'] = 'Qualifikationsprozess';
                $input['camp_user_id'] = $camp_user->id;
                $user = User::find($camp_user['user_id']);
                $input['slug'] = Str::uuid();
                $input['survey_status_id'] = SurveyStatus::Neu;
                $survey = Survey::create($input);
                foreach ($chapters as $chapter) {
                    $input['chapter_id'] = $chapter->id;
                    $input['survey_id'] = $survey->id;
                    $survey_chapter = SurveyChapter::create($input);
                    $questions = Question::where('chapter_id', $chapter->id)->get();
                    foreach ($questions as $question) {
                        $input['survey_chapter_id'] = $survey_chapter->id;
                        $input['question_id'] = $question->id;
                        $input['answer_first_id'] = $answer->id;
                        $input['answer_second_id'] = $answer->id;
                        $input['answer_leader_id'] = $answer->id;
                        SurveyQuestion::create($input);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Survey $survey)
    {
        $this->authorize('manage', $survey);
        $users = Auth::user()->camp->participants->pluck('username', 'id')->all();
        $leaders = User::where('role_id', Role::Gruppenleiter)->pluck('username', 'id')->all();
        $survey_statuses_id = SurveyStatusModel::pluck('name', 'id')->all();
        $user_id = $survey->campUser->user();
        $title = 'Qualifikation bearbeiten';

        $help = Help::where('title', $title)->first();
        $help['main_route'] = '/admin/surveys';
        $help['main_title'] = 'Qualifikationen';

        return view('admin.surveys.edit', compact('survey', 'users', 'leaders', 'survey_statuses_id', 'user_id', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);
        $this->authorize('manage', $survey);
        $request->validate(['survey_status_id' => ['required', Rule::enum(SurveyStatus::class)]]);
        if (! Auth::user()->demo) {
            $status = SurveyStatus::from($request->integer('survey_status_id'));
            // "Antworten zurücksetzen": zurück auf Neu leert beide Runden, zurück auf 1. offen die zweite.
            if ($request->has('update')) {
                if ($status === SurveyStatus::Neu) {
                    Helper::clearsurvey($survey, 'first');
                }
                if ($status->isFirstRound()) {
                    Helper::clearsurvey($survey, 'second');
                }
            }
            $survey->update(['survey_status_id' => $status]);
        }

        return redirect('/admin/surveys');
    }

    public function downloadPDF()
    {
        $camp = Auth::user()->camp;
        $surveys = $camp->surveys()->with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])->get()->sortBy('campuser.user.username')->values();

        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.compare_pdf', compact('surveys', 'camp', 'labels', 'datasets'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Survey $survey)
    {
        $this->authorize('manage', $survey);
        if (! Auth::user()->demo) {
            $survey->delete();
        }

        return redirect('/admin/surveys');
    }
}
