<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyQuestion extends Model
{
    //
    use HasFactory;
    
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

    public function survey_chapter(){
        return $this->belongsTo('App\SurveyChapter');
    } 

    public function isCoreCompetence($camp){
        return ($this->question['competence_js1'] && $camp['camp_type_id']===config('status.camptype_JS1')) ||
                                    ($this->question['competence_js2'] && $camp['camp_type_id']===config('status.camptype_JS2'));
    }

    public function competence_text(){
        $camp = Auth::user()->camp;
        $competence = Competence::where('camp_type_id', $camp['camp_type_id'])->where('question_id', $this->question['id'])->first();
        if($competence){
            return $competence['name'];
        }
        else{
            return '';
        }
    }

}
