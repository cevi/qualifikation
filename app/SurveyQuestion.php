<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    //
    protected $fillable = [
    'survey_chapter_id', 'question_id', 'comment', 'answer_id', 'comment_leader', 'answer_leader_id'
    ];

    public function answer(){
        return $this->belongsTo('App\Answer', 'answer_id');
    } 

    public function answer_leader(){
        return $this->belongsTo('App\Answer', 'answer_leader_id', 'id');
    } 

    public function question(){
        return $this->belongsTo('App\Question');
    } 

}
