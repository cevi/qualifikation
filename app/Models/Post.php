<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment', 'user_id', 'leader_id', 'file', 'camp_id', 'show_on_survey',
    ];

    protected $casts = [
        'show_on_survey' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function leader()
    {
        return $this->belongsTo('App\Models\User', 'leader_id', 'id');
    }

    public function camp()
    {
        return $this->belongsTo('App\Models\Camp');
    }
}
