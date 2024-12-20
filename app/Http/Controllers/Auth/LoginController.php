<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserCreated;
use App\Exceptions\InvalidLoginProviderException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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

    public function username()
    {
        return 'email';
    }

    public function redirectToHitobitoOAuth()
    {
        return Socialite::driver('hitobito')->setScopes(['name', 'with_roles'])->redirect();
    }

    public function handleHitobitoOAuthCallback(Request $request)
    {
        if ($request->error) {
            // User has denied access in Hitobito
            return $this->redirectWithError('Zugriff in Cevi-DB verweigert.');
        }
        try {
            $socialiteUser = Socialite::driver('hitobito')->setRequest($request)->setScopes(['name'])->user();
            $user = $this->findOrCreateSocialiteUser($socialiteUser);
        } catch (InvalidStateException $exception) {
            // User has reused an old link or modified the redirect?
            return $this->redirectWithError('Etwas hat nicht geklappt. Versuche es noch einmal.');
        } catch (InvalidLoginProviderException $exception) {
            return $this->redirectWithError('Melde dich bitte wie üblich mit Benutzernamen und Passwort an.');
        } catch (Exception $exception) {
            return $this->redirectWithError('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere dich mit einem klassischen Account.');
        }

        $this->guard()->login($user);

        return $this->sendLoginResponse($request);
    }

    public function redirectToHitobitoJEMKOAuth()
    {
        return Socialite::driver('hitobito_jemk')->setScopes(['name'])->redirect();
    }

    public function handleHitobitoJEMKOAuthCallback(Request $request)
    {
        if ($request->error) {
            // User has denied access in Hitobito
            return $this->redirectWithError('Zugriff in JEMK-DB verweigert.');
        }
        try {
            $socialiteUser = Socialite::driver('hitobito_jemk')->setRequest($request)->setScopes(['name'])->user();
            $user = $this->findOrCreateSocialiteUser($socialiteUser);
        } catch (InvalidStateException $exception) {
            // User has reused an old link or modified the redirect?
            return $this->redirectWithError('Etwas hat nicht geklappt. Versuche es noch einmal.');
        } catch (InvalidLoginProviderException $exception) {
            return $this->redirectWithError('Melde dich bitte wie üblich mit Benutzernamen und Passwort an.');
        } catch (Exception $exception) {
            return $this->redirectWithError('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere dich mit einem klassischen Account.');
        }

        $this->guard()->login($user);

        return $this->sendLoginResponse($request);
    }

    private function redirectWithError($error)
    {
        return Redirect::route('login')->withErrors([
            'hitobito' => [$error],
        ]);
    }

    private function findOrCreateSocialiteUser(SocialiteUser $socialiteUser)
    {
        if ($userFromDB = User::where('foreign_id', $socialiteUser->getId())->first()) {
            // User is logging in
            return $this->updateEmailIfAppropriate($userFromDB, $socialiteUser);
        } else {
            // User is registering
            return $this->createNewHitobitoUser($socialiteUser);
        }
    }

    private function updateEmailIfAppropriate(User $user, SocialiteUser $socialiteUser)
    {
        $hitobitoEmail = $socialiteUser->getEmail();
        if ($user->email != $hitobitoEmail && User::where('email', $hitobitoEmail)->doesntExist()) {
            //Update email only if it is not occupied by someone else
            $user->email = $hitobitoEmail;
            $user->save();
        }

        return $user;
    }

    private function createNewHitobitoUser(SocialiteUser $socialiteUser)
    {
        if (User::where('email', $socialiteUser->getEmail())->exists()) {
            // Don't register a new user if another account already uses the same email address
            throw new InvalidLoginProviderException;
        }
        $user = User::create([
            'foreign_id' => $socialiteUser->getId(),
            'email' => $socialiteUser->getEmail(),
            'username' => $socialiteUser->getNickname(),
            'avatar' => $socialiteUser->getAvatar(),
            'email_verified_at' => Carbon::now(), ]);
        UserCreated::dispatch($user);

        return $user;
    }
}
