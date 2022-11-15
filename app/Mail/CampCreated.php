<?php

namespace App\Mail;

use App\Models\Camp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $camp;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Camp $camp)
    {
        //
        $this->camp = $camp;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $camp = $this->camp;

        return $this->markdown('emails.camps.created', ['camp' => $this->camp])
            ->to($this->user['email'], $this->user['username'])
            ->bcc(config('mail.camp.address'), config('mail.camp.name'))
            ->subject('Du hast einen neuen Kurs erstellt.');
    }
}
