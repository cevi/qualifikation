<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bug', 'feedback', 'issue'
    ];

    protected $casts = [
        'bug' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
