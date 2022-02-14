<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'question_id', 'camp_type_id'
    ];

    public function question(){
        return $this->belongsTo('App\Models\Question');
    } 

    public function camp_type(){
        return $this->belongsTo('App\Models\CampType');
    } 
}
