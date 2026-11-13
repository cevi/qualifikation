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

    /** Ab so vielen Stufen Unterschied gilt eine Einschätzung der Leitenden als Abweichung. */
    public const DIFFERENCE_STEPS = 2;

    /**
     * Abweichung (Vergleich und Druckansicht): Leitende und die letzte offene Runde der Selbsteinschätzung liegen
     * mindestens DIFFERENCE_STEPS Stufen auseinander. Ist die 2. Runde offen, zählt nur sie, auch wenn sie noch auf
     * der Vorgabe «0» steht; sonst die 1. Runde.
     */
    public function differsFromLeader(bool $secondRoundOpen): bool
    {
        $self = $secondRoundOpen ? $this->answer_second : $this->answer_first;

        return $self && $this->answer_leader && abs($this->answer_leader['count'] - $self['count']) >= self::DIFFERENCE_STEPS;
    }

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

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'question_id', 'question_id');
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
