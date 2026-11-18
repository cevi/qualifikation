<?php

namespace App\Providers;

use App\Enums\SurveyStatus;
use App\Models\Survey;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        // Menü unten: Teilnehmende springen direkt in ihre Quali (ausfüllen bzw. nach dem Abschliessen Vergleich).
        View::composer('includes.bottomnav', function ($view) {
            $user = auth()->user();
            $surveyLink = null;
            if ($user?->camp && $user->isTeilnehmer()) {
                $survey = Survey::whereHas('campUser', fn ($query) => $query->where('camp_id', $user->camp->id)->where('user_id', $user->id))->first();
                if ($survey?->status()->isClosedByParticipant()) {
                    $surveyLink = route('survey.compare', $survey->slug);
                } elseif ($survey?->TNIsAllowed()) {
                    $surveyLink = route('survey.survey', $survey->slug);
                }
            }
            $view->with('surveyLink', $surveyLink);
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

    }
}
