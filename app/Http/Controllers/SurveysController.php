<?php

namespace App\Http\Controllers;

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
        $surveys = Survey::where('user_id', $user['id'])->orWhereIn('user_id', $users)->get();
        $survey = Survey::FindOrFail($id);
        $user_survey = $survey->user;
        $survey = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader'])->where('user_id', $user_survey['id'])->first();
        $answers = Answer::all();
        return view('home.survey', compact('user','surveys','survey', 'answers'));
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $answers = $request->answers;
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
        $surveys = Survey::where('user_id', $user['id'])->orWhereIn('user_id', $users)->get();
        $survey = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader', 'user', 'responsible'])->where('user_id', $id)->first();
        return view('home.compare', compact('user','surveys','survey'));
    }
}
