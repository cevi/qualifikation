<?php

namespace App\Policies;

use App\Models\Competence;
use App\Models\User;

class CompetencePolicy
{
    /**
     * Kompetenz und die zugeordnete Frage müssen zu einem eigenen Kurstyp gehören.
     */
    public function manage(User $user, Competence $competence): bool
    {
        return CampTypePolicy::owns($user, $competence->camp_type)
            && CampTypePolicy::owns($user, $competence->question?->camp_type);
    }
}
