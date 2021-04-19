<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $fillable = [
    'name', 'number', 'competence', 'chapter_id', 'competence_js1', 'competence_js2', 'description'
    ];

    protected $casts = [
        'competence_js1' => 'boolean',
        'competence_js2' => 'boolean',
    ];

    public function chapter(){
        return $this->belongsTo('App\Chapter');
    }

}
