<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    //
    use HasFactory;
    
    protected $fillable = [
        'survey_status_id', 'slug', 'camp_user_id'
    ];

    protected $casts = [
        'demo' => 'boolean',
    ];

    public function campUser(){
        return $this->belongsTo('App\CampUser');
    }

    public function MySurvey(){
        $aktUser = Auth::user();
        $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->first();
        return ($this->camp_user_id === $camp_user['id'] || $this->camp_user->leader_id === $aktUser['id']);
    }

    public function SurveyName(){
        $aktUser = Auth::user();
        $name = "Qualifikation";
        if($aktUser->isTeilnehmer() && $this['survey_status_id'] < config('status.survey_tnAbgeschlossen') ){
            $name = $this['survey_status_id'] <= config('status.survey_1offen') ? "1. Selbsteinschätzung" : "2. Selbsteinschätzung";
        }
        return $name;
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

    public function TNisAllowed(){
        $aktUser = Auth::user();
        $result = $this['survey_status_id'] < config('status.survey_fertig');
        if($result && $aktUser->isTeilnehmer()){
            $result = $this['survey_status_id'] <= config('status.survey_1offen');
            if(!$result){
                $camp = $aktUser->camp;
                $result = $camp['secondsurveyopen'];
            }
        }
        return $result;
    } 


    public function getRouteKeyName()
    {
        return 'slug';
    }
}
