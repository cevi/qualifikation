@auth
    <button type="button" data-dropdown-toggle="dropdown-curses" class="justify-center items-center py-2 px-4 mr-2 text-sm font-medium dark:text-white bg-primary-700 rounded-lg sm:inline-flex hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-hidden dark:focus:ring-primary-800">
        <span class="flex-1 ml-3 text-left whitespace-nowrap">            
            @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                {{Auth::user()->camp['name']}}
            @else
                Meine Kurse
            @endif 
        </span>
        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/> 
        </svg>
    </button>
    <div
        class="hidden z-50 my-4 w-56 text-base list-none navbar-background divide-y divide-gray-100 shadow-xs dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
        id="dropdown-curses">
        <ul aria-labelledby="dropdown-curses" class="h-dropdown py-1 text-gray-700 dark:text-gray-300 overflow-y-auto" >
            @if(!Auth::user()->demo )
                <li>
                    <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                    href="{{ route('home.camps.create') }}">
                        Kurs erstellen
                    </a>
                </li>
            @endif
            @if(count(Auth::user()->camps) > 0)
                <hr class="h-px bg-gray-400 border-0 dark:bg-gray-200">
            @endif
            @foreach (Auth::user()->camps as $camp)
                @if(!$camp['global_camp'])
                    <li class="container">
                        <div class="row">
                            <div class="col-10">
                                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                href="{{route('home.camps.update',$camp['id'])  }}"
                                    onclick="event.preventDefault();
                                                                document.getElementById('camps-update-form-{{$camp['id']}}').submit();">
                                    {{$camp['name']}}
                                </a>
                            </div>
                            <div class="col-2">
                                @if(!Auth::user()->demo && $camp->user['id']===Auth::user()->id)
                                <a class="block py-2 text-center text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                    href="{{route('admin.camps.edit',$camp)  }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endif
                            </div>
                        </div>

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
