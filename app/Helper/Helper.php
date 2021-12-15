<?php

namespace App\Helper;

use App\Camp;
use App\User;
use App\Answer;
use App\CampUser;

class Helper
{
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

    static function updateCamp(User $user, Camp $camp, $create = false)
    {
        if($create){
            $camp_global = Camp::where('global_camp', true)->first();
            CampUser::firstOrCreate([
                'camp_id' => $camp_global['id'],
                'user_id' => $user['id'],
                'role_id' => config('status.role_Teilnehmer'),
            ]);

        }
        $camp_user = CampUser::firstOrCreate(['camp_id' => $camp->id, 'user_id' =>$user->id]);
        if($create){
            $camp_user->update([
                'role_id' => $user['role_id'],
                'leader_id' => $user['leader_id'], 
            ]);
        }
        if( $camp_user->leader){
            $leader_id = $camp_user->leader->id;
        }
        else{
            $leader_id = null;
        }
        $user->update([
            'camp_id' => $camp->id, 
            'leader_id' => $leader_id, 
            'role_id' => $camp_user->role->id]);
    }
}