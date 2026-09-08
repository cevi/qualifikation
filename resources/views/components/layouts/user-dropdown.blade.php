@auth
    <button type="button" class="min-w-8 flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
        <span class="sr-only">Usermenü öffnen</span>
        <img
            class="w-8 h-8 rounded-full"
            src="{{Auth::user()->getAvatar()}}"
            alt="user photo"
        />
    </button>
    <!-- Dropdown menu -->
    <div
    class="hidden z-50 my-4 w-56 text-base list-none navbar-background rounded-xs divide-y divide-gray-100 shadow-md dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
    id="dropdown"
    >
        <div class="py-3 px-4">
            <span class="block text-sm font-semibold text-gray-900 dark:text-white"> {{ Auth::user()->username }} </span>
            <span class="block text-sm text-gray-900 truncate dark:text-white"> {{ Auth::user()->email }} </span>
        </div>
        <ul class="py-1 text-gray-700 dark:text-gray-300 m-0" aria-labelledby="dropdown">
            <li class="mx-1">
                <a href="{{ route('home.user',Auth::user()->slug) }}" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left" >
                    Profil
                </a>
            </li>
            <hr class="my-1 border-gray-200 dark:border-gray-600">
            <li class="mx-1">
                <a href="{{ route('logout') }}"
                   class="block py-2 px-3 text-sm rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-950/30 dark:hover:text-red-300 transition-colors duration-150 text-left"
                   onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                    style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
@else
    {{-- <li> --}}
        <a href="{{ route('login') }}" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Login</a>
    {{-- </li> --}}
    @if (Route::has('register'))
        {{-- <li> --}}
            <a href="{{ route('register') }}" class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Registrieren</a>
        {{-- </li> --}}
    @endif
@endauth
