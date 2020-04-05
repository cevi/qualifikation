<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyChapter extends Model
{
    //
    protected $fillable = [
    'chapter_id', 'survey_id'
    ];

    public function chapter(){
        return $this->belongsTo('App\Chapter');
    } 

    public function questions(){
        return $this->hasMany('App\SurveyQuestion');
    } 
}
