<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">

        @auth
            <a class="navbar-brand" href="{{ url('/home') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @else
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @endauth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    @if (Auth::user()->isCampleader())
                        <li>
                            <a class="nav-link nav-item" href="/admin">Dashboard<span class="caret"></span></a>
                        </li>
                    @endif
                    @if (!Auth::user()->isTeilnehmer())
                       @isset($users)
                            @if(count($users)>0)
                                <li class="nav-item dropdown">
                                    <a id="UserDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Teilnehmer <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="UserDropdown">
                                        @if ($users)
                                            <ul class="list-unstyled">
                                                @foreach ($users as $user_profile)
                                                    <li>
                                                        <a class="nav-link" href="{{route('home.profile', $user_profile->slug)}}">{{$user_profile->leader_id === Auth::user()->id ? '*' : ''}}{{$user_profile->username}}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endisset
                        <li>
                            <a class="nav-link nav-item" href="/post">Rückmeldungen<span class="caret"></span></a>
                        </li>
                    @endif
                @endauth

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarCampDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                                {{Auth::user()->camp['name']}}
                            @else
                                Meine Kurse
                            @endif <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarCampDropdown">
                            @if(!Auth::user()->demo )
                                <a class="dropdown-item" href="{{ route('home.camps.create') }}">
                                    Kurs erstellen
                                </a>
                            @endif
                            @foreach (Auth::user()->camps as $camp)
                                @if(!$camp['global_camp'])
                                    <a class="dropdown-item" href="{{route('home.camps.update',$camp['id'])  }}"
                                        onclick="event.preventDefault();
                                                    document.getElementById('camps-update-form-{{$camp['id']}}').submit();">
                                        {{$camp['name']}}
                                    </a>

                                    <form id="camps-update-form-{{$camp['id']}}" action="{{route('home.camps.update',$camp['id'])  }}" method="POST" style="display: none;">
                                        {{ method_field('PUT') }}
                                        @csrf
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->username }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('home.user',Auth::user()->slug) }}">
                                Profil
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
            </ul>
        </div>
    </div>
</nav>
