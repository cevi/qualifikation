<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Listeners\MakeUserSlug;
use App\Listeners\CreateCampUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            MakeUserSlug::class,
            CreateCampUser::class,
        ],
        'Illuminate\Auth\Events\Verified' => [
            'App\Listeners\LogVerifiedUser',
        ],
        // \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        //     // add your listeners (aka providers) here
        //     'SocialiteProviders\\Zoho\\ZohoExtendSocialite@handle',
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
