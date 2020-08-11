<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\Answer;
use App\Survey;
use App\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveysController extends Controller
{
    //
    public function survey($id)
    {
        $user = Auth::user();
        $users = User::where('leader_id',$user['id'])->pluck('id')->all();
        $surveys = Survey::where(function($query) use ($user){
            $query->where('user_id', $user['id']);
            $query->whereIn('survey_status_id', [config('status.survey_neu'),  config('status.survey_offen')]);
            })
            ->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        $survey = Survey::FindOrFail($id);
        $user_survey = $survey->user;
        if(((!$user->isLeader()) && (!$user->isCampLeader()) && $survey['survey_status_id'] > config('status.survey_offen'))){
            return redirect(404);
        }
        else{
            if(((!$user->isLeader()) && (!$user->isCampLeader()) && $user_survey['id'] != $user['id'])){
                return redirect(404);
            }
            else{
                if((($user->isLeader()) && $user_survey['leader_id'] != $user['id'])){
                    return redirect(404);
                }
                else{
                    $survey = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader'])->where('user_id', $user_survey['id'])->first();
                }
            }
        }
        $answers = Answer::all();
        $camp = Camp::FindOrFail($user['camp_id']);
        return view('home.survey', compact('user','surveys','survey', 'answers' ,'camp'));
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $survey = Survey::findOrFail($id);
        $answers = $request->answers;
        if(!$user->isLeader()){
            if ($survey['survey_status_id']===config('status.survey_neu')){
                $survey->update(['survey_status_id' => config('status.survey_offen')]);    
            }
        }
        foreach($answers as $index => $answer){
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if($user->isLeader()){
                $surveyquestion->update(['answer_leader_id' => $answer]);
            }
            else
            {
                $surveyquestion->update(['answer_id' => $answer]);
            }
        }
        
        $comments = $request->comments;
        foreach($comments as $index => $comment){
            $surveyquestion = SurveyQuestion::findOrFail($index);
            if($user->isLeader()){
                $surveyquestion->update(['comment_leader' => $comment]);
            }
            else
            {
                $surveyquestion->update(['comment' => $comment]);
            }
        }
        return redirect()->back();

    }

    public function compare($id)
    {
        $user = Auth::user();
        if($user->isCampleader()){
            $users = User::where('camp_id',$user['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$user['id'])->pluck('id')->all();
        }
        $surveys = Survey::where('user_id', $user['id'])->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        $survey = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader', 'user', 'responsible'])->where('user_id', $id)->first();
        $camp = Camp::FindOrFail($user['camp_id']);
        if(((!$user->isLeader()) && (!$user->isCampLeader()) && $id != $user['id'])){
            return redirect(404);
        }
        else{
            return view('home.compare', compact('user','surveys','survey','camp'));
        }
    }

    public function finish($id)
    {
        $survey = Survey::findOrFail($id);
        $user = Auth::user();
        if ($survey['survey_status_id']===config('status.survey_offen')){
            $survey->update(['survey_status_id' => config('status.survey_abgeschlossen')]);    
        }
        if ($survey['survey_status_id']===config('status.survey_abgeschlossen') && $user->isleader()){
            $survey->update(['survey_status_id' => config('status.survey_fertig')]);    
        }
        return redirect()->back();
    }
}
