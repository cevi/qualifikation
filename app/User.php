<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'role_id', 'is_active', 'camp_id', 'leader_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function camp(){
        return $this->belongsTo('App\Camp');
    } 
    
    public function leader(){
        return $this->belongsTo('App\User');
    }

    public function isAdmin(){
        if($this->role['is_admin'] == 1 && $this->is_active == 1){
            return true;
        }
        return false;
    }

    public function isCampleader(){
        if(($this->role['is_campleader'] == 1 || $this->role['is_admin'] == 1) && $this->is_active == 1){
            return true;
        }
        return false;
    }

    public function own_survey(){
        return $this->hasMany('App\Survey', 'user_id', 'user_id');
    }

    public function responsible_survey(){
        return $this->hasMany('App\Survey', 'responsible_id', 'user_id');
    }
}
