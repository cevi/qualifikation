<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;

class ChapterPolicy
{
    public function manage(User $user, Chapter $chapter): bool
    {
        return CampTypePolicy::owns($user, $chapter->camp_type);
    }
}
