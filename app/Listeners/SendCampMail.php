<?php

namespace App\Listeners;

use App\Events\CampCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        Mail::raw('Hallo, es wurde ein neues Lager erstellt: ' . $event->camp['name'], function ($message) {
            $message
                ->to(config('mail.camp.address'), config('mail.camp.name'))
                ->subject('Neues Lager erstellt');
});

    }
}
