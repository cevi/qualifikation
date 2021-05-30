<?php

namespace App\Helper;

use App\User;
use App\Answer;

class Helper
{
    static function getUsers($aktUser)
    {
        $users = [];
        if($aktUser){
            if(!$aktUser->isTeilnehmer()){
                $users = User::where('camp_id',$aktUser['camp_id'])->where('role_id', config('status.role_Teilnehmer'))->get();
            }
        }else{
            $users = null;
        }
        return $users;
    }

    static function clearSurvey($survey, $which)
    {
        $questions = $survey->questions;
        $answer = Answer::where('name','0')->first();
        foreach($questions as $question){
            if($which == 'first'){
                $question->update(['answer_first_id' => $answer['id'], 'comment_first' => '']);  
            }
            $question->update(['answer_second_id' => $answer['id'], 'comment_second' => '']); 
        }
    }
}