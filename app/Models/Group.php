<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'shortname', 'campgroup', 'foreign_id', 'api_token'
    ];

    protected $casts = [
        'campgroup' => 'boolean',
    ];
}
