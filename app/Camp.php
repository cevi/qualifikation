<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    //

    protected $fillable = [
        'name', 'year', 'camp_status_id', 'user_id'
    ];

    public function users(){
        return $this->hasMany('App\User');
    }

    public function camp_status(){
        return $this->belongsTo('App\CampStatus');
    }

    public function user(){
        return $this->belongsTo('App\User');
    } 
}
