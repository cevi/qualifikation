<?php

namespace App\Policies;

use App\Models\CampType;
use App\Models\User;

class CampTypePolicy
{
    /**
     * Eigene Kurstypen samt Kapiteln, Fragen und Kompetenzen bearbeitet nur, wer sie erstellt hat.
     * Standard-Kurstypen und Inhalte ohne Kurstyp (Standardkapitel) gehören allen Kursen und damit den Admins.
     */
    public static function owns(User $user, ?CampType $campType): bool
    {
        return $user->isAdmin()
            || ($campType && ! $campType->default_type && $campType->user_id == $user->id);
    }

    public function manage(User $user, CampType $campType): bool
    {
        return self::owns($user, $campType);
    }
}
