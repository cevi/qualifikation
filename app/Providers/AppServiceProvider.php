<?php

namespace App\Providers;

use App\Enums\SurveyStatus;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        //
        // Status der Quali (alte Qualis ohne Status gelten als neu).
        Blade::if('isFirstSurvey', function (?SurveyStatus $status) {
            return ($status ?? SurveyStatus::Neu)->isFirstRound();
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

    }
}
