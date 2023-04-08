<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $fillable = [
        'name', 'number', 'competence', 'chapter_id', 'competence_js1', 'competence_js2', 'description', 'camp_type_id'
    ];

    protected $casts = [
        'competence_js1' => 'boolean',
        'competence_js2' => 'boolean',
    ];

    public function chapter()
    {
        return $this->belongsTo('App\Models\Chapter');
    }


    public function camp_type()
    {
        return $this->belongsTo('App\Models\CampType');
    }
}
