@auth
    <li class="nav-item dropdown">
        <a id="navbarCampDropdown" class="nav-link dropdown-toggle" href="#" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                {{Auth::user()->camp['name']}}
            @else
                Meine Kurse
            @endif <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarCampDropdown">
            @if(!Auth::user()->demo )
                <a class="nav-link" href="{{ route('home.camps.create') }}">
                    Kurs erstellen
                </a>
            @endif
            @foreach (Auth::user()->camps as $camp)
                @if(!$camp['global_camp'])
                    <a class="nav-link" href="{{route('home.camps.update',$camp['id'])  }}"
                       onclick="event.preventDefault();
                                                    document.getElementById('camps-update-form-{{$camp['id']}}').submit();">
                        {{$camp['name']}}
                    </a>

                    <form id="camps-update-form-{{$camp['id']}}"
                          action="{{route('home.camps.update',$camp['id'])  }}" method="POST"
                          style="display: none;">
                        {{ method_field('PUT') }}
                        @csrf
                    </form>
                @endif
            @endforeach
        </div>
    </li>
@endauth
