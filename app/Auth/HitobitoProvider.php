<?php

namespace App\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class HitobitoProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base URL under which the OAuth service is available.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $baseUrl
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(Request $request, $baseUrl, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
        $this->baseUrl = $baseUrl.'/oauth';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl.'/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->baseUrl.'/token';
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl.'/profile', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'X-Scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $avatar = Arr::get($user, 'picture') === null ?
        'https://db.cevi.ch/packs/media/images/profil-d4d04543c5d265981cecf6ce059f2c5d.png' :
        'https://db.cevi.ch/uploads/person/picture/'.$user['id'].'/'.Arr::get($user, 'picture');

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => Arr::get($user, 'nickname') ??
                Arr::get($user, 'first_name') ??
                Arr::get($user, 'last_name') ??
                Arr::first(explode('@', Arr::get($user, 'email'))),
            'email' => Arr::get($user, 'email'),
            'avatar' => $avatar,
        ]);
    }
}
