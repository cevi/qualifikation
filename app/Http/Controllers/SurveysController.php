<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\Help;
use App\Models\Post;
use App\Helper\Helper;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\CampUser;
use Illuminate\Http\Request;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Auth;

class SurveysController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function survey(Survey $survey)
    {
        $aktUser = Auth::user();
        if ($survey->TNisAllowed()) {
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader'])->where('id', $survey['id'])->get()->sortBy('user.username')->values();
        } else {
            return redirect('/home');
        }
        $answers = Answer::all();
        $posts = Post::where('user_id', $survey->campUser->user['id'])->where('show_on_survey', true)->get();
        $camp = Camp::FindOrFail($aktUser['camp_id']);
        $title = 'Qualifikation';
        $group = $survey->campUser->user->group['shortname'] ?? '';
        $subtitle = $survey->campUser->user['username'] . " " . $group;
        $help = Help::where('title',$title)->first();


        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.survey', compact('aktUser', 'surveys','labels', 'datasets', 'answers', 'camp', 'posts', 'title', 'subtitle', 'help'));
    }

    public function update(Request $request, Survey $survey)
    {
        $aktUser = Auth::user();

        $camp = $aktUser->camp;
        $answers = $request->answers;

        foreach ($answers as $index => $answer) {
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if ($aktUser->isLeader()) {
                $surveyquestion->update(['answer_leader_id' => $answer]);
            } else {
                if ($survey['survey_status_id'] < config('status.survey_2offen')) {
                    $surveyquestion->update(['answer_first_id' => $answer]);
                } else {
                    $surveyquestion->update(['answer_second_id' => $answer]);
                }
            }
        }

        $comments = $request->comments;
        foreach ($comments as $index => $comment) {
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if ($aktUser->isLeader()) {
                $surveyquestion->update(['comment_leader' => $comment]);
            } else {
                if ($survey['survey_status_id'] < config('status.survey_2offen')) {
                    $surveyquestion->update(['comment_first' => $comment]);
                } else {
                    $surveyquestion->update(['comment_second' => $comment]);
                }
            }
        }

        if (!$aktUser->isLeader()) {
            if ($request->action === 'close') {
                if (($survey['survey_status_id'] < config('status.survey_2offen') && $camp->secondsurveyopen)) {
                    $survey->update(['survey_status_id' => config('status.survey_2offen')]);
                } else {
                    $survey->update(['survey_status_id' => config('status.survey_tnAbgeschlossen')]);
                }

                return redirect('/home');
            } elseif ($survey['survey_status_id'] === config('status.survey_neu')) {
                $survey->update(['survey_status_id' => config('status.survey_1offen')]);
            }
        }
        else{
            $survey->update(['comment' => $request['comment']]);
        }

        return redirect()->refresh();
    }

    public function compare(Survey $survey)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp()->first();
        $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $camp['id'])->first();
        $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user'])->where('id', $survey->id)->get()->values();

        if ($aktUser->isTeilnehmer() && $camp_user->user->id != $aktUser['id']) {
            return redirect()->back();
        } else {
            $answers = Answer::all();
            $posts = Post::where('user_id', $survey->campUser->user['id'])->where('show_on_survey', true)->get();
            $title = 'Vergleich';
            $group = $survey->campUser->user->group['shortname'] ?? '';
            $subtitle = $survey->campUser->user['username'] . " " . $group;
            $help = Help::where('title',$title)->first();
            $labels = Helper::GetSurveysLabels($surveys);
            $datasets = Helper::GetSurveysDataset($surveys);

            return view('home.compare', compact('aktUser', 'surveys','labels', 'datasets', 'camp', 'answers', 'posts', 'title', 'subtitle', 'help'));
        }
    }

    public function downloadPDF(Survey $survey)
    {
        $camp = Auth::user()->camp;
        $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])->where('id', $survey->id)->get()->sortBy('user.username')->values();


        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.compare_pdf', compact('surveys', 'camp', 'labels' , 'datasets'));
    }

    public function finish($id)
    {
        $survey = Survey::findOrFail($id);
        $user = Auth::user();

        if ($survey['survey_status_id'] === config('status.survey_tnAbgeschlossen') && $user->isleader()) {
            $survey->update(['survey_status_id' => config('status.survey_fertig')]);
        }

        return redirect()->back();
    }
}
