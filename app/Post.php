<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment', 'user_id', 'leader_id'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function leader(){
        return $this->belongsTo('App\User', 'leader_id', 'id');
    } 
}
