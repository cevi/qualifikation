<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function responsible(){
        return $this->hasOne('App\User', 'responsible_id');
    } 
}
