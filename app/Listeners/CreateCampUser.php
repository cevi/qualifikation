<?php

namespace App\Listeners;

use App\Models\Camp;
use App\Models\CampUser;
use App\Events\UserCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $camp_user = CampUser::firstOrCreate(['camp_id' => $camp['id'], 'user_id' => $event->user['id']]);
        $camp_user->update([
            'role_id' => config('status.role_Teilnehmer')
        ]);
        $event->user->update([
            'camp_id' => $camp['id'],
            'role_id' => config('status.role_Teilnehmer'),
        ]);
    }
}
