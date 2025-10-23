<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardText extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'title', 'content', 'camp_id', 'global'
    ];

    
    protected $casts = [
        'global' => 'boolean',
    ];

    public function camp()
    {
        return $this->belongsTo('App\Models\Camp');
    }
    
}
