<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    //
    protected $fillable = [
    'name', 'number'
    ];

    public function question(){
        return $this->hasMany('App\Question');
    }
}
