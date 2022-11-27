@auth
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ Auth::user()->username }} <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="nav-link" href="{{ route('home.user',Auth::user()->slug) }}">
                Profil
            </a>
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                  style="display: none;">
                @csrf
            </form>
        </div>
    </li>
@else
    <li>
        <a href="{{ route('login') }}" class="nav-link nav-item">Login</a>
    </li>
    @if (Route::has('register'))
        <li>
            <a href="{{ route('register') }}" class="nav-link nav-item">Registrieren</a>
        </li>
    @endif
@endauth
