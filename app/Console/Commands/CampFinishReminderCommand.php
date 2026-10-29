<?php

namespace App\Console\Commands;

use App\Mail\CampFinishReminder;
use App\Services\CampChecks;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Am letzten Tag jedes Monats (app/Console/Kernel.php): Erinnerung an die Kursleitung aller Kurse, deren Schlussdatum mehr als
 * CampChecks::FINISH_AFTER_MONTHS Monate zurückliegt und die noch offen sind, eine Mail pro Person. Demo-, Global-Kurse
 * und Kurse ohne Schlussdatum nie.
 */
class CampFinishReminderCommand extends Command
{
    protected $signature = 'camps:finish-reminder';

    protected $description = 'Erinnert die Kursleitung vergangener, offener Kurse daran, den Kurs abzuschliessen';

    public function handle(CampChecks $checks): int
    {
        $sent = 0;
        // Eine Mail pro Kursleitung, auch bei mehreren offenen Kursen
        foreach ($checks->campsToFinish()->groupBy('user_id') as $camps) {
            $user = $camps->first()->user;
            if (! $user?->email) {
                $this->warn('Ohne Kursleitung mit E-Mail: '.$camps->pluck('name')->implode(', '));

                continue;
            }
            Mail::send(new CampFinishReminder($user, $camps->sortBy('end_date')->values()));
            $sent++;
        }
        $this->info("$sent Erinnerung(en) verschickt");

        return Command::SUCCESS;
    }
}
