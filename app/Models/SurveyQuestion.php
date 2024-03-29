<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SurveyQuestion extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'survey_chapter_id', 'question_id', 'comment_second', 'comment_first', 'answer_first_id', 'answer_second_id', 'comment_leader', 'answer_leader_id',
    ];

    public function answer_first()
    {
        return $this->belongsTo('App\Models\Answer', 'answer_first_id', 'id');
    }

    public function answer_second()
    {
        return $this->belongsTo('App\Models\Answer', 'answer_second_id', 'id');
    }

    public function answer_leader()
    {
        return $this->belongsTo('App\Models\Answer', 'answer_leader_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function survey_chapter()
    {
        return $this->belongsTo('App\Models\SurveyChapter');
    }

    public function competence_text()
    {
        $camp = Auth::user()->camp;
        $competence = Competence::where('camp_type_id', $camp['camp_type_id'])->where('question_id', $this->question['id'])->first();
        if ($competence) {
            return $competence['name'];
        } else {
            return '';
        }
    }
}
