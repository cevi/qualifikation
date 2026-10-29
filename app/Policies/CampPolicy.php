<?php

namespace App\Policies;

use App\Models\Camp;
use App\Models\User;

class CampPolicy
{
    /**
     * Kursleitende verwalten nur ihren aktuellen Kurs, Admins alle.
     */
    public function manage(User $user, Camp $camp): bool
    {
        return $user->isAdmin() || ($user->isCampleader() && $camp->id == $user->camp_id);
    }
}
