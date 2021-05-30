<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\Answer;
use App\Helper\Helper;
use App\Survey;
use App\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\Help;

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
        // $survey = Survey::FindOrFail($id);
        $user_survey = $survey->user;
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
                    $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader'])->where('user_id', $user_survey['id'])->get()->sortBy('user.username')->values();
                }
            }
        }
        $users = Helper::getUsers($aktUser);
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
                return redirect('/');
            }
            else if ($survey['survey_status_id'] === config('status.survey_neu')){
                $survey->update(['survey_status_id' => config('status.survey_1offen')]); 
            }
        }
        return redirect()->refresh();
        
    }

    public function compare(User $user)
    {
        $aktUser = Auth::user();
        if($aktUser->isCampleader()){
            $users = User::where('camp_id',$aktUser['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$aktUser['id'])->pluck('id')->all();
        }
        $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader', 'user'])->where('user_id', $user->id)->get()->sortBy('user.username')->values();
        $camp = Camp::FindOrFail($aktUser['camp_id']);
        if($aktUser->isTeilnehmer() && $user->id != $aktUser['id']){
            return redirect()->back();
        }
        else{
            $users = Helper::getUsers($aktUser);
            return view('home.compare', compact('aktUser','surveys','camp', 'users'));
        }
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
