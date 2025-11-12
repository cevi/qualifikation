<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment', 'leader_id', 'file', 'camp_id', 'camp_user_id', 'show_on_survey', 'uuid'
    ];

    protected $casts = [
        'show_on_survey' => 'boolean',
    ];

    public function campUser()
    {
        return $this->belongsTo('App\Models\CampUser');
    }

    public function leader()
    {
        return $this->belongsTo('App\Models\User', 'leader_id', 'id');
    }

    public function filename()
    {
        $name =  basename($this->file);
        $uuid = Str::before($name,'_');
        if(Str::isuuid($uuid)){
            $name =  Str::after($name,'_');
        }
        return $name;
    }
}
