@auth
    <button
        type="button"
        data-dropdown-toggle="dropdown-curses"
        class="flex items-center justify-between py-2 pr-4 pl-3 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-150 text-sm xl:text-base focus:outline-hidden focus:ring-0 focus:border-0"
    >
        <span>            
            @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                {{Auth::user()->camp['name']}}
            @else
                Meine Kurse
            @endif 
        </span>
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div
        class="hidden z-50 my-4 w-56 text-base list-none navbar-background divide-y divide-gray-100 shadow-md dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
        id="dropdown-curses">
        <ul aria-labelledby="dropdown-curses" class="max-h-[85vh] py-1 text-gray-700 dark:text-gray-300 overflow-y-auto m-0" >
            @if(!Auth::user()->demo)
                <li class="mx-1">
                    <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                    href="{{ route('home.camps.create') }}">
                        Kurs erstellen
                    </a>
                </li>
            @else
                <li class="mx-1">
                    <span class="block py-2 px-3 text-sm rounded-lg text-gray-400 dark:text-gray-500 cursor-not-allowed select-none text-left" title="Im Demo-Modus deaktiviert">
                        Kurs erstellen <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(im Demo-Modus inaktiv)</span>
                    </span>
                </li>
            @endif
            @if(count(Auth::user()->camps) > 0)
                <hr class="my-1 border-gray-200 dark:border-gray-600">
            @endif
            @foreach (Auth::user()->camps as $camp)
                @if(!$camp['global_camp'])
                    <li class="flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150 rounded-lg mx-1">
                        <a class="grow block py-2 px-3 text-sm text-gray-700 dark:text-gray-300 dark:hover:text-white text-left truncate"
                           href="{{route('home.camps.update',$camp['id'])  }}"
                           onclick="event.preventDefault();
                                    document.getElementById('camps-update-form-{{$camp['id']}}').submit();">
                            {{$camp['name']}}
                        </a>
                        @if(!Auth::user()->demo && $camp->user['id']===Auth::user()->id)
                            <a class="py-2 px-3 text-gray-400 hover:text-blue-600 dark:hover:text-white transition-colors duration-150 flex items-center justify-center"
                               href="{{route('admin.camps.edit',$camp)  }}">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                        @endif

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
