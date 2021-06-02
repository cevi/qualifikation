<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        
        @guest
        @else
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
            </a>
        @endguest
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @guest
                @else
                    @if (Auth::user()->isCampleader())
                        <li>
                            <a class="nav-link nav-item" href="/admin">Dashboard<span class="caret"></span></a> 
                        </li>           
                    @endif
                    @if (!Auth::user()->isTeilnehmer())
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
                @endguest

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @else
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
                @endguest
            </ul>
        </div>
    </div>
</nav>