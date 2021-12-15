<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\Answer;
use App\Survey;
use App\CampUser;
use App\SurveyQuestion;
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
        $users = User::where('leader_id',$aktUser['id'])->pluck('id')->all();
        
        $user_survey = $survey->campUser->user;
        $surveys = [];
        if(($aktUser->isTeilnehmer() && $survey['survey_status_id'] > config('status.survey_2offen'))){
            return redirect()->back();
        }
        else{
            if(($aktUser->isTeilnehmer() && $user_survey['id'] != $aktUser['id'])){
                return redirect()->back();
            }
            else{
                if((($aktUser->isLeader()) && $user_survey['leader_id'] != $aktUser['id'])){
                    return redirect()->back();
                }
                else{
                    $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader'])->where('id', $survey['id'])->get()->sortBy('user.username')->values();
                }
            }
        }
        $users = $aktUser->camp->participants;
        $answers = Answer::all();
        $camp = Camp::FindOrFail($aktUser['camp_id']);
        return view('home.survey', compact('aktUser','surveys', 'answers' ,'camp', 'users'));
    }
    
    public function update(Request $request, Survey $survey)
    {
        $aktUser = Auth::user();
        // $survey = Survey::findOrFail($id);
        $answers = $request->answers;

        foreach($answers as $index => $answer){
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if($aktUser->isLeader()){
                $surveyquestion->update(['answer_leader_id' => $answer]);
            }
            else
            {
                if ($survey['survey_status_id'] < config('status.survey_2offen')){
                    $surveyquestion->update(['answer_first_id' => $answer]);
                }
                else {
                    $surveyquestion->update(['answer_second_id' => $answer]);
                }
            }
        }
        
        $comments = $request->comments;
        foreach($comments as $index => $comment){
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if($aktUser->isLeader()){
                $surveyquestion->update(['comment_leader' => $comment]);
            }
            else
            {
                if ($survey['survey_status_id'] < config('status.survey_2offen')){
                    $surveyquestion->update(['comment_first' => $comment]);
                }
                else {
                    $surveyquestion->update(['comment_second' => $comment]);
                }
            }
        }

        if(!$aktUser->isLeader()){
            if($request->action === 'close'){
                if ($survey['survey_status_id'] < config('status.survey_2offen')){
                    $survey->update(['survey_status_id' => config('status.survey_2offen')]); 
                }
                else {
                    $survey->update(['survey_status_id' => config('status.survey_tnAbgeschlossen')]); 
                }
                return redirect('/home');
            }
            else if ($survey['survey_status_id'] === config('status.survey_neu')){
                $survey->update(['survey_status_id' => config('status.survey_1offen')]); 
            }
        }
        return redirect()->refresh();
        
    }

    public function compare(Survey $survey)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $camp['id'])->first();
        $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader', 'campuser.user'])->where('id', $survey->id)->get()->values();
 
        if($aktUser->isTeilnehmer() && $camp_user->user->id != $aktUser['id']){
            return redirect()->back();
        }
        else{
            $users = $camp->participants;
            $answers = Answer::all();
            return view('home.compare', compact('aktUser','surveys','camp', 'users', 'answers'));
        }
    }

    public function downloadPDF(Survey $survey)
    {
        $camp = Auth::user()->camp;
        $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])->where('id', $survey->id)->get()->sortBy('user.username')->values();
        return view('home.compare_pdf', compact('survey','surveys', 'camp'));
    }

    public function finish($id)
    {
        $survey = Survey::findOrFail($id);
        $user = Auth::user();
        if ($survey['survey_status_id'] === config('status.survey_tnAbgeschlossen') && $user->isleader()){
            $survey->update(['survey_status_id' => config('status.survey_fertig')]);    
        }
        return redirect()->back();
    }
}
