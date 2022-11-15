<?php

namespace App\Helper;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\User;

class Helper
{
    public static function clearSurvey($survey, $which)
    {
        $questions = $survey->questions;
        $answer = Answer::where('name', '0')->first();
        foreach ($questions as $question) {
            if ($which == 'first') {
                $question->update(['answer_first_id' => $answer['id'], 'comment_first' => '']);
            }
            $question->update(['answer_second_id' => $answer['id'], 'comment_second' => '']);
        }
    }

    public static function updateCamp(User $user, Camp $camp)
    {
        $camp_user = CampUser::firstOrCreate(['camp_id' => $camp->id, 'user_id' => $user->id]);
        $leader_id = $camp['global_camp'] ? null : $user['leader_id'];
        $role_id = $camp_user['role_id'];
        if ($role_id === null) {
            $role_id = $camp['global_camp'] ? config('status.role_Teilnehmer') : $user['role_id'];
        }
        $classification_id = $camp_user['classification_id'] ?: config('status.classification_yellow');
        $camp_user->update([
            'role_id' => $role_id,
            'leader_id' => $leader_id,
            'classification_id' => $classification_id,
        ]);
        if ($camp_user->leader) {
            $leader_id = $camp_user->leader->id;
        } else {
            $leader_id = null;
        }
        $user->update([
            'camp_id' => $camp->id,
            'leader_id' => $leader_id,
            'role_id' => $camp_user->role->id, ]);
    }
}
