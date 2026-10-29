<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Frage und ihr Kapitel müssen zu einem eigenen Kurstyp gehören.
     */
    public function manage(User $user, Question $question): bool
    {
        return CampTypePolicy::owns($user, $question->camp_type)
            && CampTypePolicy::owns($user, $question->chapter?->camp_type);
    }
}
