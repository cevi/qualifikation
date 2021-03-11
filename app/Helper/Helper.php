<?php

namespace App\Helper;

use App\User;

class Helper
{
    static function getUsers($aktUser)
    {
        $users = [];
        if(!$aktUser->isTeilnehmer()){
            $users = User::where('camp_id',$aktUser['camp_id'])->where('role_id', config('status.role_Teilnehmer'))->get();
        }
        return $users;
    }
}