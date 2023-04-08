<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    //
    protected $fillable = [
        'name', 'number', 'camp_type_id'
    ];

    public function question()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function camp_type()
    {
        return $this->belongsTo('App\Models\CampType');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class,CampType::class,
            'id','id','camp_type_id','user_id');
    }
}
