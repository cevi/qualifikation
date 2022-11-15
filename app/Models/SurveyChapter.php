<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyChapter extends Model
{
    //
    protected $fillable = [
        'chapter_id', 'survey_id',
    ];

    public function chapter()
    {
        return $this->belongsTo('App\Models\Chapter');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\SurveyQuestion');
    }
}
