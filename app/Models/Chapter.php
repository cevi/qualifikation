<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    //
    protected $fillable = [
    'name', 'number'
    ];

    public function question(){
        return $this->hasMany('App\Models\Question');
    }
}
