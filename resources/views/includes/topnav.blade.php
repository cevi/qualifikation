<nav class="dark:bg-gray-800 border-b border-gray-200 px-4 lg:px-6 py-2.5 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            @auth
                <a class="navbar-brand" href="{{ url('/home') }}"  class="flex items-center">
                    <img src="/img/logo.svg" alt="QualiTool Logo" class="mr-3 h-6 lg:h-12">
                </a>
            @else
                <a class="navbar-brand" href="{{ url('/') }}"  class="flex items-center">
                    <img src="/img/logo.svg" alt="QualiTool Logo" class="mr-3 h-6 lg:h-12">
                </a>
            @endauth
        </div>
        <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
            <ul class="flex flex-col font-medium lg:flex-row lg:space-x-8">
                @auth
                @if (Auth::user()->isCampleader())
                    <li>
                        <a href="/admin"
                        type="button"
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        >
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">       
                                Dashboard
                            </span>
                        </a>
                    </li>
                @endif
                @if (!Auth::user()->isTeilnehmer())
                    @isset($users)
                        @if(count($users)>0)
                            <li>
                                <button
                                    type="button"
                                    class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                    id="users-menu-button"
                                    aria-expanded="false"
                                    data-dropdown-toggle="dropdown-users"
                                    >
                                    <span class="flex-1 ml-3 text-left whitespace-nowrap">            
                                        Teilnehmer
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
                            </li>
                            <div
                                class="hidden z-50 my-4 w-56 text-base list-none navbar-background rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
                                id="dropdown-users"
                            >
                                @if ($users)
                                    <ul aria-labelledby="dropdown-users" class="py-1 text-gray-700 dark:text-gray-300">
                                        @foreach ($users as $user_profile)
                                            <li>
                                                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                                                    href="{{route('home.profile', $user_profile->slug)}}">{{$user_profile->leader_id === Auth::user()->id ? '*' : ''}}{{$user_profile->username}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    @endisset
                    <li>
                        <a href="/post"
                        type="button"
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        >
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">       
                                Rückmeldungen
                            </span>
                        </a>
                    </li>
                @endif
            @endauth
            </ul>
        </div>
        <x-right-navbar/>
    </div>
</nav>