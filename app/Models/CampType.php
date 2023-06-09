<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampType extends Model
{
    //

    protected $fillable = [
        'name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function competences()
    {
        return $this->hasMany(Competence::class);
    }
}
