<?php

namespace App\Models;

use App\Enums\Classification;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'camp_id', 'user_id', 'role_id', 'leader_id', 'classification_id', 'avatar',
    ];

    protected $casts = [
        'role_id' => Role::class,
        'classification_id' => Classification::class,
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function camp()
    {
        return $this->belongsTo('App\Models\Camp');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function leader()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function classification()
    {
        return $this->belongsTo('App\Models\Classification');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    /** Rückmeldungen zu dieser Person in diesem Kurs. */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
