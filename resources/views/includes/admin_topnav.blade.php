<header class="header">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
                <div class="navbar-nav navbar-header">
                    <a id="toggle-btn" href="#" class="menu-btn">
                        <i class="fas fa-bars"></i> </a>
                    </div>
                    <div class="navbar-nav navbar-header">
                    <a href="{{ url('/admin') }}">
                        <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
                    </a>
                </div>
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
</header>