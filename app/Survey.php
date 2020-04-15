<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
    protected $fillable = [
        'name', 'user_id', 'responsible_id', 'survey_status_id'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function responsible(){
        return $this->belongsTo('App\User', 'responsible_id', 'id');
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
