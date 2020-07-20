<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    //

    protected $fillable = [
        'name', 'year', 'user_id', 'camp_type_id'
    ];

    public function users(){
        return $this->hasMany('App\User');
    }

    public function user(){
        return $this->belongsTo('App\User');
    } 

    public function camp_type(){
        return $this->belongsTo('App\CampType');
    } 
}
