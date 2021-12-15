<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Authenticatable  implements MustVerifyEmail
{
    use Notifiable;
    use SearchableTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'role_id', 'is_active', 'camp_id', 'leader_id', 'password_change_at', 
        'avatar', 'classification_id', 'slug', 'group_id', 'foreign_id', 'email', 'email_verified_at'
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
        'is_active' => 'boolean',
        'demo' => 'boolean',
    ];

    protected $searchable = [
        'columns' => [
            'username' => 1,
        ]
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function camp(){
        return $this->belongsTo('App\Camp');
    } 

    public function camps(){
        return $this->belongsToMany('App\Camp', 'camp_users');
    } 
    
    public function leader(){
        return $this->belongsTo('App\User');
    }

    public function leaders(){
        return $this->belongsToMany('App\User', 'camp_users');
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

    public function classification(){
        return $this->belongsTo('App\Classification');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
