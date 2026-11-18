<?php

namespace App\Http\Controllers;

use App\Enums\SurveyStatus;
use App\Helper\Helper;
use App\Models\Answer;
use App\Models\Camp;
use App\Models\Help;
use App\Models\Post;
use App\Models\Survey;
use Illuminate\Http\Request;
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
        $aktUser = Auth::user();
        $camp = $aktUser->camp;

        if ($survey->TNIsAllowed()) {
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader'])->where('id', $survey['id'])->get()->sortBy('user.username')->values();
        } else {
            return redirect('/home');
        }
        $answers = Answer::all();
        $posts = Post::where('camp_user_id', $survey->campUser['id'])->where('show_on_survey', true)->get();
        $camp = Camp::FindOrFail($aktUser['camp_id']);
        $title = 'Qualifikation';
        $group = $survey->campUser->user->group['shortname'] ?? '';
        $subtitle = $survey->campUser->user['username'].' '.$group;
        $help = Help::where('title', $title)->first();

        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.survey', compact('aktUser', 'surveys', 'labels', 'datasets', 'answers', 'camp', 'posts', 'title', 'subtitle', 'help'));
    }

    public function update(Request $request, Survey $survey)
    {
        // Gleiche Bedingung wie für das Anzeigen des Formulars in survey().
        abort_unless($survey->TNIsAllowed(), 403);
        $aktUser = Auth::user();

        $camp = $aktUser->camp;
        $answers = $request->answers ?? [];

        foreach ($answers as $index => $answer) {
            $surveyquestion = $survey->questions()->findOrFail($index);
            if ($aktUser->isLeader()) {
                $surveyquestion->update(['answer_leader_id' => $answer]);
            } else {
                if (! $survey->status()->hasReachedSecondRound()) {
                    $surveyquestion->update(['answer_first_id' => $answer]);
                } else {
                    $surveyquestion->update(['answer_second_id' => $answer]);
                }
            }
        }

        $comments = $request->comments ?? [];
        foreach ($comments as $index => $comment) {
            $surveyquestion = $survey->questions()->findOrFail($index);
            if ($aktUser->isLeader()) {
                $surveyquestion->update(['comment_leader' => $comment]);
            } else {
                if (! $survey->status()->hasReachedSecondRound()) {
                    $surveyquestion->update(['comment_first' => $comment]);
                } else {
                    $surveyquestion->update(['comment_second' => $comment]);
                }
            }
        }

        if (! $aktUser->isLeader()) {
            if ($request->action === 'close') {
                if (! $survey->status()->hasReachedSecondRound() && $camp->secondsurveyopen) {
                    $survey->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
                } else {
                    $survey->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
                }

                return redirect('/home');
            } elseif ($survey->status() === SurveyStatus::Neu) {
                $survey->update(['survey_status_id' => SurveyStatus::ErsteRundeOffen]);
            }
        } else {
            $survey->update(['comment' => $request['comment']]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->refresh();
    }

    public function compare(Survey $survey)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp()->first();

        if (! $survey->SurveyIsAllowed()) {
            return redirect('/home');
        } else {
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user'])->where('id', $survey->id)->get()->values();
            $answers = Answer::all();
            $posts = Post::where('camp_user_id', $survey->campUser['id'])->where('show_on_survey', true)->get();
            $title = 'Vergleich';
            $group = $survey->campUser->user->group['shortname'] ?? '';
            $subtitle = $survey->campUser->user['username'].' '.$group;
            $help = Help::where('title', $title)->first();
            $labels = Helper::GetSurveysLabels($surveys);
            $datasets = Helper::GetSurveysDataset($surveys);

            return view('home.compare', compact('aktUser', 'surveys', 'labels', 'datasets', 'camp', 'answers', 'posts', 'title', 'subtitle', 'help'));
        }
    }

    public function downloadPDF(Survey $survey)
    {
        // Wie die Vergleichsseite. Die Druckansicht zeigt die Leiter-Einschätzung, deshalb nicht für TN
        // (die Vergleichsseite bietet ihnen den Link auch nicht an).
        abort_unless($survey->SurveyIsAllowed() && ! Auth::user()->isTeilnehmer(), 403);
        $camp = Auth::user()->camp;
        $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])->where('id', $survey->id)->get()->sortBy('user.username')->values();

        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.compare_pdf', compact('surveys', 'camp', 'labels', 'datasets'));
    }

    public function finish($id)
    {
        $survey = Survey::findOrFail($id);
        // Gleiche Bedingung wie für das Anzeigen der Vergleichsseite in compare().
        abort_unless($survey->SurveyIsAllowed(), 403);
        $user = Auth::user();

        if ($survey->status() === SurveyStatus::TnAbgeschlossen && $user->isleader()) {
            $survey->update(['survey_status_id' => SurveyStatus::Fertig]);
        }

        return redirect()->back();
    }
}
