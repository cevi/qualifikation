<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Rückmeldungen bearbeitet und löscht nur, wer sie geschrieben hat (wie in components/posts/post.blade.php).
     */
    public function manage(User $user, Post $post): bool
    {
        return $post->leader_id == $user->id;
    }
}
