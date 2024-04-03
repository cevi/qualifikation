@auth
    <button
        type="button"
        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
        id="curse-menu-button"
        aria-expanded="false"
        data-dropdown-toggle="dropdown-curses"
        >
        <span class="flex-1 ml-3 text-left whitespace-nowrap">            
            @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                {{Auth::user()->camp['name']}}
            @else
                Meine Kurse
            @endif 
        </span>
        <svg
            aria-hidden="true"
            class="w-6 h-6"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd">
            </path>
        </svg>
    </button>
    <div
        class="hidden z-50 my-4 w-56 text-base list-none navbar-background rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
        id="dropdown-curses">
        <ul aria-labelledby="dropdown-curses" class="py-1 text-gray-700 dark:text-gray-300">
            @if(!Auth::user()->demo )
                <li>
                    <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                    href="{{ route('home.camps.create') }}">
                        Kurs erstellen
                    </a>
                </li>
            @endif
            @foreach (Auth::user()->camps as $camp)
                @if(!$camp['global_camp'])
                    <li>
                        <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                        href="{{route('home.camps.update',$camp['id'])  }}"
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
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endauth
