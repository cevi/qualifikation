<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Camp;
use App\Models\CampUser;

class CreateCampUser
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
     * @param  object  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        //
        $camp = Camp::where('global_camp', true)->first();
        $camp_user = CampUser::firstOrCreate(['camp_id' => $camp['id'], 'user_id' => $event->user['id'], 'classification_id' => config('status.classification_yellow')]);
        $camp_user->update([
            'role_id' => config('status.role_Teilnehmer'),
        ]);
        if (! $event->user->camp) {
            $event->user->update([
                'camp_id' => $camp['id'],
            ]);
        }
        if (! $event->user->role) {
            $event->user->update([
                'role_id' => config('status.role_Teilnehmer'),
            ]);
        }
    }
}
