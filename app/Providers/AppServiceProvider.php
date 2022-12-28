<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::if('isFirstSurvey', function ($value) {
            return $value <= config('status.survey_1offen');
        });

    }
}
