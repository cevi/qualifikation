<?php

namespace App\Policies;

use App\Models\CampUser;
use App\Models\User;

class UserPolicy
{
    /**
     * Kursleitende verwalten die Personen ihres aktuellen Kurses, aber nie einen Admin
     * (der Admin ist über Camp::created() in jedem Kurs eingetragen).
     */
    public function manage(User $user, User $person): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCampleader()
            && ! $person->isAdmin()
            && CampUser::where('camp_id', $user->camp_id)->where('user_id', $person->id)->exists();
    }
}
