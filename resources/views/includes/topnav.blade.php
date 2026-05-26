@auth
    @php
        $camp = Auth::user()->camp;
    @endphp
@endauth
<nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 px-4 xl:px-6 py-2.5 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex shrink-0 justify-start items-center">
            @auth
                <a class="navbar-brand flex items-center" href="{{ url('/home') }}">
                    <x-layouts.logo/>
                </a>
            @else
                <a class="navbar-brand flex items-center" href="{{ url('/') }}">
                    <x-layouts.logo/>
                </a>
            @endauth
        </div>
        <x-layouts.right-navbar/>

        <!-- Desktop Navigation Menu (visible only on desktop screen widths) -->
        <div class="hidden xl:flex xl:items-center xl:w-auto xl:order-1">
            <ul class="flex flex-row xl:space-x-4 2xl:space-x-8 font-medium items-center m-0 p-0">
                @auth
                    <!-- Home -->
                    <li>
                        <a href="/home" class="block py-2 pr-4 pl-3 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-150 text-sm xl:text-base">
                            Home
                        </a>
                    </li>

                    <!-- Teilnehmer (Dropdown) -->
                    @if (!Auth::user()->isTeilnehmer())
                        @isset($camp)
                            @if($camp->participants()->count() > 0)
                                <li>
                                    <button
                                        type="button"
                                        class="flex items-center justify-between py-2 pr-4 pl-3 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-150 text-sm xl:text-base focus:outline-hidden focus:ring-0 focus:border-0"
                                        id="users-menu-button"
                                        aria-expanded="false"
                                        data-dropdown-toggle="dropdown-users"
                                    >
                                        <span>Teilnehmer</span>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </li>
                                <div
                                    class="hidden z-50 my-4 w-56 text-base list-none navbar-background divide-y divide-gray-100 shadow-md dark:bg-gray-700 dark:divide-gray-600 rounded-xl max-h-[85vh] overflow-y-auto"
                                    id="dropdown-users"
                                >
                                    @if(count($camp->my_participants)>0)
                                        <ul aria-labelledby="dropdown-users" class="py-1 text-gray-700 dark:text-gray-300 m-0">
                                            @foreach ($camp->my_participants as $user_profile)
                                                <li class="mx-1">
                                                    <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                                        href="{{route('home.profile', $user_profile->slug)}}">{{$user_profile->username}} {{$user_profile->group['shortname'] ?? ''}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(count($camp->other_participants)>0 && count($camp->my_participants)>0)
                                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                                    @endif
                                    @if(count($camp->other_participants)>0)
                                        <ul aria-labelledby="dropdown-users" class="py-1 text-gray-700 dark:text-gray-300 m-0">
                                            @foreach ($camp->other_participants as $user_profile)
                                                <li class="mx-1">
                                                    <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                                        href="{{route('home.profile', $user_profile->slug)}}">{{$user_profile->username}} {{$user_profile->group['shortname'] ?? ''}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        @endisset

                        <!-- Rückmeldungen -->
                        <li>
                            <a href="/posts" class="block py-2 pr-4 pl-3 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-150 text-sm xl:text-base">
                                Rückmeldungen
                            </a>
                        </li>
                    @endif

                    <!-- Kursadmin (Dropdown) -->
                    @if (Auth::user()->isCampleader())
                        <li>
                            <button
                                type="button"
                                class="flex items-center justify-between py-2 pr-4 pl-3 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white transition-colors duration-150 text-sm xl:text-base focus:outline-hidden focus:ring-0 focus:border-0"
                                id="kursadmin-menu-button"
                                aria-expanded="false"
                                data-dropdown-toggle="dropdown-kursadmin"
                            >
                                <span>Kursadmin</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </li>
                        <div
                            class="hidden z-50 my-4 w-80 text-base list-none navbar-background divide-y divide-gray-100 shadow-md dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
                            id="dropdown-kursadmin"
                        >
                            <ul aria-labelledby="dropdown-kursadmin" class="py-1 text-gray-700 dark:text-gray-300 m-0">
                                <li class="mx-1">
                                    <a href="/admin" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                        <i class="fas fa-chart-line w-4 text-center mr-2"></i>
                                        <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                            Kurs-Dashboard
                                        </span>
                                    </a>
                                </li>
                                @if (isset(Auth::user()->camp) || Auth::user()->isAdmin())
                                    <li class="mx-1">
                                        <a href="{{route('admin.users.index')}}" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                            <i class="fas fa-users w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Personen
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                <li class="mx-1">
                                    <a href="{{route('surveys.index')}}" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                        <i class="fas fa-poll-h w-4 text-center mr-2"></i>
                                        <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                            Qualifikationen
                                        </span>
                                    </a>
                                </li>
                                <hr class="my-1 border-gray-200 dark:border-gray-600">
                                <li class="mx-1">
                                    <a href="{{route('admin.camps.index')}}" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                        <i class="fas fa-campground w-4 text-center mr-2"></i>
                                        <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                            Kurs
                                        </span>
                                    </a>
                                </li>
                                @if (Auth::user()->isAdmin() || !Auth::user()->camp->camp_type['default_type'])
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('admin.camp_types.index')}}"
                                        >
                                            <i class="fas fa-campground w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Kurs-Typen
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('chapters.index')}}"
                                        >
                                            <i class="fas fa-edit w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Kapitel
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('questions.index')}}"
                                        >
                                            <i class="fas fa-edit w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Kompetenzen
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('competences.index')}}"
                                        >
                                            <i class="fas fa-edit w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Kernkompetenzen
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                <li class="mx-1">
                                    <a href="{{route('admin.standard_texts.index')}}" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                        <i class="far fa-keyboard w-4 text-center mr-2"></i>
                                        <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                            Standard-Texte
                                        </span>
                                    </a>
                                </li>
                                @if (Auth::user()->isAdmin())
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('answers.index')}}"
                                        >
                                            <i class="fas fa-edit w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Antworten
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('classifications.index')}}"
                                        >
                                            <i class="fas fa-edit w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Klassifizierungen
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="/admin/groups"
                                        >
                                            <i class="fas fa-campground w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Gruppen
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="{{route('helps.index')}}"
                                        >
                                            <i class="fa-solid fa-question w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Hilfe-Artikel
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mx-1">
                                        <a class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left"
                                           href="/admin/feedback"
                                        >
                                            <i class="fas fa-clipboard-list w-4 text-center mr-2"></i>
                                            <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                                Feedbacks
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                <li class="mx-1">
                                    <a href="/admin/changes" class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 text-left">
                                        <i class="fas fa-clipboard-list w-4 text-center mr-2"></i>
                                        <span class="flex-1 ml-1 text-left whitespace-nowrap">
                                            Rückmeldungen / Änderungen
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                @endauth
            </ul>
        </div>

        <!-- Mobile Navigation Menu Sheet (visible only on mobile/tablet screens, toggleable) -->
        <div class="hidden w-full xl:hidden" id="mobile-menu-2">
            <ul class="flex flex-col font-medium space-y-4 mt-6 w-full px-2">
                @auth
                    <!-- Kurs Auswahl (Camp Selector) -->
                    <li>
                        <button
                            type="button"
                            class="flex justify-between items-center w-full py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0 focus:border-0"
                            data-collapse-toggle="mobile-dropdown-curses"
                        >
                            <span>
                                @if(Auth::user()->camp && !Auth::user()->camp['global_camp'] )
                                    {{Auth::user()->camp['name']}}
                                @else
                                    Meine Kurse
                                @endif
                            </span>
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="mobile-dropdown-curses" class="hidden border-l-2 border-gray-200 dark:border-gray-700 pl-4 mt-2 space-y-3 max-h-[50vh] overflow-y-auto">
                            @if(!Auth::user()->demo)
                                <a class="block py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                href="{{ route('home.camps.create') }}">
                                    Kurs erstellen
                                </a>
                            @else
                                <span class="block py-1.5 text-base text-gray-400 dark:text-gray-500 cursor-not-allowed select-none" title="Im Demo-Modus deaktiviert">
                                    Kurs erstellen <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(im Demo-Modus inaktiv)</span>
                                </span>
                            @endif
                            @if(count(Auth::user()->camps) > 0)
                                <hr class="border-gray-100 dark:border-gray-800">
                            @endif
                            @foreach (Auth::user()->camps as $camp)
                                @if(!$camp['global_camp'])
                                    <div class="flex items-center justify-between">
                                        <a class="block py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                        href="{{route('home.camps.update',$camp['id'])  }}"
                                            onclick="event.preventDefault();
                                                                        document.getElementById('mobile-camps-update-form-{{$camp['id']}}').submit();">
                                            {{$camp['name']}}
                                        </a>
                                        @if(!Auth::user()->demo && $camp->user['id']===Auth::user()->id)
                                        <a class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            href="{{route('admin.camps.edit',$camp)  }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @endif
                                    </div>
                                    <form id="mobile-camps-update-form-{{$camp['id']}}"
                                            action="{{route('home.camps.update',$camp['id'])  }}" method="POST"
                                            style="display: none;">
                                        {{ method_field('PUT') }}
                                        @csrf
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </li>

                    <!-- Home -->
                    <li>
                        <a href="/home" class="block py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0">
                            Home
                        </a>
                    </li>

                    <!-- Teilnehmer Collapsible -->
                    @if (!Auth::user()->isTeilnehmer())
                        @isset($camp)
                            @if($camp->participants()->count() > 0)
                                <li>
                                    <button
                                        type="button"
                                        class="flex justify-between items-center w-full py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0 focus:border-0"
                                        data-collapse-toggle="mobile-dropdown-users"
                                    >
                                        <span>Teilnehmer</span>
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="mobile-dropdown-users" class="hidden border-l-2 border-gray-200 dark:border-gray-700 pl-4 mt-2 space-y-3 max-h-[50vh] overflow-y-auto">
                                        @if(count($camp->my_participants) > 0)
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mt-1">Meine Teilnehmer</div>
                                            @foreach ($camp->my_participants as $user_profile)
                                                <a class="block py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                    href="{{route('home.profile', $user_profile->slug)}}">
                                                    {{$user_profile->username}} {{$user_profile->group['shortname'] ?? ''}}
                                                </a>
                                            @endforeach
                                        @endif
                                        
                                        @if(count($camp->other_participants) > 0)
                                            @if(count($camp->my_participants) > 0)
                                                <hr class="border-gray-100 dark:border-gray-800">
                                            @endif
                                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Andere Teilnehmer</div>
                                            @foreach ($camp->other_participants as $user_profile)
                                                <a class="block py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                    href="{{route('home.profile', $user_profile->slug)}}">
                                                    {{$user_profile->username}} {{$user_profile->group['shortname'] ?? ''}}
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endisset

                        <!-- Rückmeldungen -->
                        <li>
                            <a href="/posts" class="block py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0">
                                Rückmeldungen
                            </a>
                        </li>
                    @endif

                    <!-- Kursadmin Collapsible -->
                    @if (Auth::user()->isCampleader())
                        <li>
                            <button
                                type="button"
                                class="flex justify-between items-center w-full py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0 focus:border-0"
                                data-collapse-toggle="mobile-dropdown-kursadmin"
                            >
                                <span>Kursadmin</span>
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="mobile-dropdown-kursadmin" class="hidden border-l-2 border-gray-200 dark:border-gray-700 pl-4 mt-2 space-y-3 max-h-[60vh] overflow-y-auto">
                                <a href="/admin" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-chart-line w-5 text-center mr-2"></i>
                                    <span>Kurs-Dashboard</span>
                                </a>
                                @if (isset(Auth::user()->camp) || Auth::user()->isAdmin())
                                    <a href="{{route('admin.users.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-users w-5 text-center mr-2"></i>
                                        <span>Personen</span>
                                    </a>
                                @endif
                                <a href="{{route('surveys.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-poll-h w-5 text-center mr-2"></i>
                                    <span>Qualifikationen</span>
                                </a>
                                <hr class="border-gray-100 dark:border-gray-800">
                                <a href="{{route('admin.camps.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-campground w-5 text-center mr-2"></i>
                                    <span>Kurs</span>
                                </a>
                                @if (Auth::user()->isAdmin() || !Auth::user()->camp->camp_type['default_type'])
                                    <a href="{{route('admin.camp_types.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-campground w-5 text-center mr-2"></i>
                                        <span>Kurs-Typen</span>
                                    </a>
                                    <a href="{{route('chapters.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-edit w-5 text-center mr-2"></i>
                                        <span>Kapitel</span>
                                    </a>
                                    <a href="{{route('questions.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-edit w-5 text-center mr-2"></i>
                                        <span>Kompetenzen</span>
                                    </a>
                                    <a href="{{route('competences.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-edit w-5 text-center mr-2"></i>
                                        <span>Kernkompetenzen</span>
                                    </a>
                                @endif
                                <a href="{{route('admin.standard_texts.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="far fa-keyboard w-5 text-center mr-2"></i>
                                    <span>Standard-Texte</span>
                                </a>
                                @if (Auth::user()->isAdmin())
                                    <a href="{{route('answers.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-edit w-5 text-center mr-2"></i>
                                        <span>Antworten</span>
                                    </a>
                                    <a href="{{route('classifications.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-edit w-5 text-center mr-2"></i>
                                        <span>Klassifizierungen</span>
                                    </a>
                                    <a href="/admin/groups" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-campground w-5 text-center mr-2"></i>
                                        <span>Gruppen</span>
                                    </a>
                                    <a href="{{route('helps.index')}}" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fa-solid fa-question w-5 text-center mr-2"></i>
                                        <span>Hilfe-Artikel</span>
                                    </a>
                                    <a href="/admin/feedback" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fas fa-clipboard-list w-5 text-center mr-2"></i>
                                        <span>Feedbacks</span>
                                    </a>
                                @endif
                                <a href="/admin/changes" class="flex items-center py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-clipboard-list w-5 text-center mr-2"></i>
                                    <span>Rückmeldungen / Änderungen</span>
                                </a>
                            </div>
                        </li>
                    @endif

                    <!-- Profil & Abmelden Collapsible -->
                    <li>
                        <button
                            type="button"
                            class="flex justify-between items-center w-full py-2 text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-hidden focus:ring-0 focus:border-0"
                            data-collapse-toggle="mobile-dropdown-profile"
                        >
                            <span class="flex items-center">
                                <img
                                    class="w-6 h-6 rounded-full mr-2"
                                    src="{{Auth::user()->getAvatar()}}"
                                    alt="user photo"
                                />
                                {{ Auth::user()->username }}
                            </span>
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="mobile-dropdown-profile" class="hidden border-l-2 border-gray-200 dark:border-gray-700 pl-4 mt-2 space-y-3">
                            <div class="text-xs text-gray-500 dark:text-gray-400 px-1 truncate">
                                {{ Auth::user()->email }}
                            </div>
                            <a href="{{ route('home.user',Auth::user()->slug) }}" class="block py-1.5 text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Profil
                            </a>
                            <a href="{{ route('logout') }}"
                               class="block py-1.5 text-base text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                               onclick="event.preventDefault();
                                        document.getElementById('mobile-logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>