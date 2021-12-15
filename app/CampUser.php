<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampUser extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'camp_id', 'user_id', 'role_id', 'leader_id'
    ];

    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function camp(){
        return $this->belongsTo('App\Camp');
    } 
    
    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function leader(){
        return $this->belongsTo('App\User');
    }
    
    public function surveys(){
        return $this->hasMany(Survey::class);
    }
}
