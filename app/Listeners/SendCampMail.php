<?php

namespace App\Listeners;

use App\Events\CampCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendCampMail
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
     * @param  \App\Events\CampCreated  $event
     * @return void
     */
    public function handle(CampCreated $event)
    {
        //
        Mail::send(new \App\Mail\CampCreated(Auth::user(), $event->camp));
    }
}
