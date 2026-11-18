<?php

namespace App\Policies;

use App\Models\StandardText;
use App\Models\User;

class StandardTextPolicy
{
    /**
     * Kursleitende verwalten die Standardtexte ihres aktuellen Kurses. Globale Texte erscheinen in allen Kursen.
     */
    public function manage(User $user, StandardText $standardText): bool
    {
        return $user->isAdmin()
            || ($user->isCampleader() && ! $standardText->global && $standardText->camp_id == $user->camp_id);
    }
}
