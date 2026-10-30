<?php

namespace App\Models;

use App\Enums\Classification;
use App\Enums\Role;
use App\Helper\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'camp_id', 'user_id', 'role_id', 'classification_id', 'avatar',
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

    /** Zuständige Leitende, alle gleichgestellt (Tabelle camp_user_leader). */
    public function leaders()
    {
        return $this->belongsToMany(User::class, 'camp_user_leader', 'camp_user_id', 'leader_id')->withTimestamps();
    }

    public function isLedBy(User $user): bool
    {
        return $this->leaders->contains($user);
    }

    /** Personen, bei denen $user zu den Leitenden gehört. */
    public function scopeLedBy(Builder $query, User $user): void
    {
        $query->whereHas('leaders', fn (Builder $leaders) => $leaders->whereKey($user->id));
    }

    /** Namen der Leitenden für Anzeigen, alphabetisch, mit Komma getrennt. */
    public function leaderNames(): string
    {
        return $this->leaders->sortBy('username', SORT_NATURAL | SORT_FLAG_CASE)->pluck('username')->implode(', ');
    }

    public function classification()
    {
        return $this->belongsTo('App\Models\Classification');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    /** Kursbild, sonst Profilbild, sonst null (ohne eigene Abfrage, wenn user geladen ist). */
    public function avatarPath(): ?string
    {
        return Helper::getAvatarPath($this->avatar) ?? Helper::getAvatarPath($this->user?->avatar);
    }

    /** Rückmeldungen zu dieser Person in diesem Kurs. */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
