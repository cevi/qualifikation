<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    //
    protected $fillable = [
    'survey_chapter_id', 'question_id', 'comment_second', 'comment_first', 'answer_first_id','answer_second_id', 'comment_leader', 'answer_leader_id'
    ];

    public function answer_first(){
        return $this->belongsTo('App\Answer', 'answer_first_id', 'id');
    } 

    public function answer_second(){
        return $this->belongsTo('App\Answer', 'answer_second_id', 'id');
    } 
    public function answer_leader(){
        return $this->belongsTo('App\Answer', 'answer_leader_id', 'id');
    } 

    public function question(){
        return $this->belongsTo('App\Question');
    } 

}
