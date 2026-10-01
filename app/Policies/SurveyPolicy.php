<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;

class SurveyPolicy
{
    /**
     * Qualis im Admin-Bereich: Kursleitende nur die ihres aktuellen Kurses, Admins alle.
     * Ausfüllen und Vergleichen regeln Survey::TNIsAllowed() und Survey::SurveyIsAllowed().
     */
    public function manage(User $user, Survey $survey): bool
    {
        return $user->isAdmin() || ($user->isCampleader() && $survey->campUser?->camp_id == $user->camp_id);
    }
}
