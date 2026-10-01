<?php

namespace App\Providers;

use App\Auth\HitobitoJEMKProvider;
use App\Auth\HitobitoProvider;
use App\Models\Camp;
use App\Models\CampType;
use App\Models\Chapter;
use App\Models\Competence;
use App\Models\Post;
use App\Models\Question;
use App\Models\StandardText;
use App\Models\Survey;
use App\Models\User;
use App\Policies\CampPolicy;
use App\Policies\CampTypePolicy;
use App\Policies\ChapterPolicy;
use App\Policies\CompetencePolicy;
use App\Policies\PostPolicy;
use App\Policies\QuestionPolicy;
use App\Policies\StandardTextPolicy;
use App\Policies\SurveyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Camp::class => CampPolicy::class,
        CampType::class => CampTypePolicy::class,
        Chapter::class => ChapterPolicy::class,
        Competence::class => CompetencePolicy::class,
        Post::class => PostPolicy::class,
        Question::class => QuestionPolicy::class,
        StandardText::class => StandardTextPolicy::class,
        Survey::class => SurveyPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->bootHitobitoSocialite();
        $this->bootHitobitoJEMKSocialite();

        //
    }

    private function bootHitobitoSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $authServiceProvider = $this;
        $socialite->extend(
            'hitobito',
            static function ($app) use ($authServiceProvider) {
                $config = $authServiceProvider->getHitobitoConfig('hitobito');

                return new HitobitoProvider(
                    $app['request'], $config['base_url'], $config['client_id'],
                    $config['client_secret'], $authServiceProvider->formatRedirectUrl($config),
                    Arr::get($config, 'guzzle', [])
                );
            }
        );
    }

    private function bootHitobitoJEMKSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $authServiceProvider = $this;
        $socialite->extend(
            'hitobito_jemk',
            static function ($app) use ($authServiceProvider) {
                $config = $authServiceProvider->getHitobitoConfig('hitobito_jemk');

                return new HitobitoJEMKProvider(
                    $app['request'], $config['base_url'], $config['client_id'],
                    $config['client_secret'], $authServiceProvider->formatRedirectUrl($config),
                    Arr::get($config, 'guzzle', [])
                );
            }
        );
    }

    public function getHitobitoConfig(string $provider): array
    {
        $config = config("services.$provider");

        if (! is_array($config)) {
            throw new \RuntimeException("Missing configuration for services.$provider. Clear and rebuild the Laravel configuration cache.");
        }

        foreach (['base_url', 'client_id', 'client_secret', 'redirect'] as $key) {
            if (blank($config[$key] ?? null)) {
                throw new \RuntimeException("Missing services.$provider.$key configuration.");
            }
        }

        return $config;
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @return string
     */
    public function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
            ? $this->app['url']->to($redirect)
            : $redirect;
    }
}
