<?php

namespace App\Mail;

use App\Models\Camp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Monatliche Erinnerung an eine Kursleitung, ihre vergangenen Kurse abzuschliessen (camps:finish-reminder).
 * Eine Mail pro Person, auch bei mehreren offenen Kursen.
 */
class CampFinishReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Camp>  $camps
     */
    public function __construct(public User $user, public Collection $camps) {}

    public function build()
    {
        $subject = $this->camps->count() === 1
            ? 'Bitte schliesse deinen Kurs «'.$this->camps->first()->name.'» ab'
            : 'Bitte schliesse deine '.$this->camps->count().' vergangenen Kurse ab';

        return $this->markdown('emails.camps.finish-reminder', ['user' => $this->user, 'camps' => $this->camps])
            ->to($this->user->email, $this->user->username)
            ->subject($subject);
    }
}
