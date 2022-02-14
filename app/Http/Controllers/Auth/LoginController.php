<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use App\Models\HitobitoUser;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Exceptions\InvalidLoginProviderException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        return '/home';
    
    }

    public function authenticated(Request $request, $user)
    {
        if (!($user['is_active'])) {
            auth()->logout();
            return back()->with('warning', 'Du musst zuerst noch freigeschalten werden.');
        }
        return redirect()->intended($this->redirectPath());
    }

    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }   

    public function username(){
        return 'username';
    }
    
    public function redirectToHitobitoOAuth()
    {
        return Socialite::driver('hitobito')->setScopes(['name, email'])->redirect();
    }

    public function handleHitobitoOAuthCallback(Request $request)
    {
        return $request;
        if ($request->error) {
            // User has denied access in Hitobito
            return $this->redirectWithError(__('t.views.login.midata.user_has_denied_access'));
        }
        try {
            $socialiteUser = Socialite::driver('hitobito')->setRequest($request)->setScopes(['name'])->user();
            $user = $this->findOrCreateSocialiteUser($socialiteUser);
        } catch (InvalidStateException $exception) {
            // User has reused an old link or modified the redirect?
            return $this->redirectWithError(__('t.views.login.midata.error_please_retry'));
        } catch (InvalidLoginProviderException $exception) {
            return $this->redirectWithError(__('t.views.login.midata.use_normal_credentials'));
        } catch (Exception $exception) {
            return $this->redirectWithError(__('t.views.login.midata.error_retry_later'));
        }

        $this->guard()->login($user);
        return $this->sendLoginResponse($request);
    }

    private function redirectWithError($error) {
        return Redirect::route('login')->withErrors([
            'hitobito' => [$error],
        ]);
    }

    private function findOrCreateSocialiteUser(SocialiteUser $socialiteUser)
    {
        if ($userFromDB = HitobitoUser::where('hitobito_id', $socialiteUser->getId())->first()) {
            // User is logging in
            return $this->updateEmailIfAppropriate($userFromDB, $socialiteUser);
        } else {
            // User is registering
            return $this->createNewHitobitoUser($socialiteUser);
        }
    }

    private function createNewHitobitoUser(SocialiteUser $socialiteUser) {
        if (User::where('email', $socialiteUser->getEmail())->exists()) {
            // Don't register a new user if another account already uses the same email address
            throw new InvalidLoginProviderException;
        }
        $created = HitobitoUser::create(['hitobito_id' => $socialiteUser->getId(), 'email' => $socialiteUser->getEmail(), 'name' => $socialiteUser->getNickname()]);
        return $created;
    }

}
