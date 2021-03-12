<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
    protected $fillable = [
        'user_id', 'survey_status_id'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function MySurvey(){
        $aktUser = Auth::user();
        return ($this->user_id === $aktUser['id'] || $this->user->leader_id === $aktUser['id']);
    }

    public function SurveyName(){
        $aktUser = Auth::user();
        if($aktUser->isTeilnehmer()){
            return $this['survey_status_id'] <= config('status.survey_1offen') ? "1. Selbsteinschätzung" : "2. Selbsteinschätzung";
        }
        else
        {
            return "Qualifizierung";
        }
    }

    public function chapters(){
        return $this->HasMany('App\SurveyChapter');
    }

    public function questions(){
        return $this->hasManyThrough('App\SurveyQuestion', 'App\SurveyChapter');
    }
    
    public function survey_status(){
        return $this->belongsTo('App\SurveyStatus');
    } 
}
