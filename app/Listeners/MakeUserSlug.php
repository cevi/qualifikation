<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Str;

class MakeUserSlug
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        //
        $event->user->update(['slug' => Str::uuid()]);
    }
}
