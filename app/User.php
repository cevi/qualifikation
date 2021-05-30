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
        'username', 'password', 'role_id', 'is_active', 'camp_id', 'leader_id', 'password_change_at', 
        'avatar', 'classification_id', 'slug', 'group_id', 'foreign_id', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token', 'password_change_at'];

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

    public function isLeader(){
        if(($this->role['is_leader']=== 1) && ($this->is_active == 1)){
            return true;
        }
        return false;
    }

    public function isTeilnehmer(){
        return (!$this->isLeader() && !$this->isCampleader());
    }

    public function own_surveys(){
        return $this->hasMany('App\Survey', 'user_id', 'id');
    }

    public function classification(){
        return $this->belongsTo('App\Classification');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
