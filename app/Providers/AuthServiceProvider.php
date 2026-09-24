<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Auth\HitobitoProvider;
use App\Auth\HitobitoJEMKProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
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

        if (!is_array($config)) {
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
     * @param  array  $config
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
