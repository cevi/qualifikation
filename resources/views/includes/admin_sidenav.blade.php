<!-- Side Navbar -->
<nav class="side-navbar">
    <div class="side-navbar-wrapper">
        <div class="main-menu">
            <h5 class="sidenav-heading">Qualifikation</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">
                <li>
                    <a href="/home"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-home"></i>
                        <span class="ml-3">Home</span>
                    </a>
                </li>
                <li>
                    <a href="/admin"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-home"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                @if ((isset(Auth::user()->camp) OR (Auth::user()->isAdmin())))
                    <li>
                        <button type="button"
                                class="flex items-center p-2 w-full text-base font-normal text-gray-900 transition duration-75 group dark:text-white  hover:bg-gray-100 dark:hover:bg-gray-700"
                                aria-controls="dropdown-persons" data-collapse-toggle="dropdown-persons">
                            <i class="fas fa-users"></i>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Personen</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </button>
                        <ul id="dropdown-persons" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{route('users.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Alle
                                    Personen</a>
                            </li>
                            @if (!Auth::user()->demo)
                                <li>
                                    <a href="{{route('users.create')}}"
                                       class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Person
                                        erstellen</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
        <div class="admin-menu">
            <h5 class="sidenav-heading">Administration</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">
                <li>
                    <a href="{{route('admin.camps.index')}}"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-campground"></i>
                        <span class="ml-3">Kurs</span>
                    </a>
                </li>
                @if (Auth::user()->isCampleader())
                    <li>
                        <a href="{{route('surveys.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-poll-h"></i>
                            <span class="ml-3">Qualifikationen</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->isAdmin())
                    <li>
                        <a href="/admin/groups"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-campground"></i>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">Gruppen</span>
                        </a>
                    </li>
                    <!-- /.nav-second-level -->
                    <li>
                        <a href="{{route('answers.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-edit"></i>
                            <span class="ml-3">Antworten</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('classifications.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-edit"></i>
                            <span class="ml-3">Klassifizierungen</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/feedback"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="ml-3">Feedbacks</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->isAdmin() || !Auth::user()->camp->camp_type['default_type'])
                    <li>
                        <a href="{{route('camp_types.index')}}"
                           class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-campground"></i>
                            <span class="ml-3">Kurs-Typen</span>
                        </a>
                    </li>
                    <li>
                        <button type="button"
                                class="flex items-center p-2 w-full text-base font-normal text-gray-900 transition duration-75 group dark:text-white  hover:bg-gray-100 dark:hover:bg-gray-700"
                                aria-controls="dropdown-questions" data-collapse-toggle="dropdown-questions">
                            <i class="fas fa-question"></i>
                            <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Kompetenzen</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </button>
                        <ul id="dropdown-questions" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{route('questions.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Kompetenzen</a>
                            </li>
                            <li>
                                <a href="{{route('chapters.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Kapitel</a>
                            </li>
                            <li>
                                <a href="{{route('competences.index')}}"
                                   class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Kernkompetenzen</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li>
                    <a href="/admin/changes"
                       class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="ml-3">Rückmeldungen / Änderungen</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

