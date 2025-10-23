<nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 px-4 xl:px-6 py-2.5 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
  <div class="flex flex-wrap justify-between items-center">
    <div class="flex justify-start items-center">
      <a class="navbar-brand" href="{{ url('/admin') }}"  class="flex items-center">
          <x-logo/>
      </a>
    </div>
    <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
      <ul class="flex flex-col font-medium lg:flex-row lg:space-x-8">
        <li>
            <a href="/home" 
              type="button"
              class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            >
              <i class="fas fa-home"></i>
              <span class="flex-1 ml-3 text-left whitespace-nowrap">       
                  Home
               </span>
            </a>
        </li>
        <li>
            <a href="/admin"
            type="button"
            class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
          >
            <i class="fas fa-home"></i>
            <span class="flex-1 ml-3 text-left whitespace-nowrap">       
                Dashboard
             </span>
          </a>
        </li>
        @if (isset(Auth::user()->camp) OR (Auth::user()->isAdmin()))
          <li>
            <a href="{{route('admin.users.index')}}"
              type="button"
              class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            >
              <i class="fas fa-users"></i>
              <span class="flex-1 ml-3 text-left whitespace-nowrap">
                Personen
              </span>
            </a>
          </li>
        @endif 
        @if (Auth::user()->isCampleader())
          <li>
            <a href="{{route('surveys.index')}}"
              type="button"
              class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            >
              <i class="fas fa-poll-h"></i>
              <span class="flex-1 ml-3 text-left whitespace-nowrap">
                Qualifikationen
              </span>
            </a>
          </li>
        @endif
        <li>
          <button
            type="button"
            class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            id="dashboard-menu-button"
            aria-expanded="false"
            data-dropdown-toggle="dropdown-dashboard"
          >
            <span class="flex-1 ml-3 text-left whitespace-nowrap">            
                Kurs-Administration
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
            class="hidden z-50 my-4 w-80 text-base list-none navbar-background rounded-xs divide-y divide-gray-100 shadow-xs dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
            id="dropdown-dashboard"
        >
          <ul aria-labelledby="dropdown-dashboard" class="py-1 text-gray-700 dark:text-gray-300">
            <li>
              <a href="{{route('admin.camps.index')}}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-campground"></i>
                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                  Kurs
                </span>
              </a>
            </li>
            @if (Auth::user()->isAdmin() || !Auth::user()->camp->camp_type['default_type'])
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('admin.camp_types.index')}}"
                >
                  <i class="fas fa-campground"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Kurs-Typen
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('chapters.index')}}"
                >
                  <i class="fas fa-edit"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Kapitel
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('questions.index')}}"
                >
                  <i class="fas fa-edit"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Kompetenzen
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('competences.index')}}"
                >
                  <i class="fas fa-edit"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Kernkompetenzen
                  </span>
                </a>
              </li>
            @endif
            <li>
              <a href="{{route('admin.standard_texts.index')}}" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                <i class="far fa-keyboard"></i>
                <span class="flex-1 ml-3 text-left whitespace-nowrap">
                  Standard-Texte
                </span>
              </a>
            </li>
            @if (Auth::user()->isAdmin())
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('answers.index')}}"
                >
                  <i class="fas fa-edit"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Antworten
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('classifications.index')}}"
                >
                  <i class="fas fa-edit"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Klassifizierungen
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="/admin/groups"
                >
                  <i class="fas fa-campground"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Gruppen
                  </span>
                </a>
              </li>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="{{route('helps.index')}}"
                >
                  <i class="fa-solid fa-question"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Hilfe-Artikel
                  </span>
                </a>
              <li>
                <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                  href="/admin/feedback"
                >
                  <i class="fas fa-clipboard-list"></i>
                  <span class="flex-1 ml-3 text-left whitespace-nowrap">
                    Feedbacks
                  </span>
                </a>
              </li>
              </li>
            @endif
            <li>
              <a href="/admin/changes"
                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
              >
                <i class="fas fa-clipboard-list"></i>
                <span class="ml-3">Rückmeldungen / Änderungen</span>
              </a>
          </li>
          </ul>
        </div>
      </ul>
    </div>
    <x-right-navbar/>
  </div>
</nav>
