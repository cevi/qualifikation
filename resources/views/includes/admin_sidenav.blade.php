<!-- Side Navbar -->
<nav class="side-navbar">
    <div class="side-navbar-wrapper">
      <!-- Sidebar Header    -->
        <div class="sidenav-header d-flex align-items-center justify-content-center">
            <!-- User Info-->
            <div class="sidenav-header-inner text-center">
                <h2 class="h5">{{Auth::user()->username}}</h2>
            </div>
            <!-- Small Brand information, appears on minimized sidebar-->
            <div class="sidenav-header-logo"><a href="/admin" class="brand-small text-center"> <strong>Q</strong><strong class="text-primary">B</strong></a></div>
        </div>
      

        <!-- Sidebar Navigation Menus-->
        <div class="main-menu">
            <h5 class="sidenav-heading">Qualifikation</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">           
                <li><a href="/"> <i class="fas fa-home"></i> Home</a></li>       
                <li><a href="/admin"> <i class="fas fa-home"></i> Dashboard</a></li>
                @if ((isset(Auth::user()->camp) OR (Auth::user()->isAdmin())))
                    <li><a href="#UsersDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-users"></i> Personen</a>
                        <ul id="UsersDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('users.index')}}">Alle Personen</a>
                            </li>

                            <li>
                            <a href="{{route('users.create')}}">Person erstellen</a>
                            </li>
                        </ul>
                            <!-- /.nav-second-level -->
                    </li>
                @endif
            </ul>
        </div>
        <div class="admin-menu">
            <h5 class="sidenav-heading">Administration</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">  
                <li><a href="{{route('camps.index')}}"> <i class="fas fa-campground"></i> Kurs</a></li>
                @if (Auth::user()->isCampleader())
                    <li><a href="{{route('surveys.index')}}">  <i class="fas fa-poll-h"></i> Qualifikationen</a>
                @endif
                <li><a href="#QuestionsDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-question"></i> Kompetenzen</a>
                    <ul id="QuestionsDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('questions.index')}}">Alle Kompetenzen</a>
                        </li>
                        @if (Auth::user()->isAdmin())
                            <li>
                                <a href="{{route('questions.create')}}">Kompetenz erstellen</a>
                            </li>
                            <li>
                                <a href="{{route('chapters.index')}}">Kapitel</a>
                            </li>
                        @endif
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                @if (Auth::user()->isAdmin())
                    <li><a href="#AnswersDropdown" aria-expanded="false" data-toggle="collapse"><i class="fas fa-edit"></i> Antworten</a>
                        <ul id="AnswersDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('answers.index')}}">Alle Antworten</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li><a href="#ClassificationsDropdown" aria-expanded="false" data-toggle="collapse"><i class="fas fa-edit"></i> Klassifizierungen</a>
                        <ul id="ClassificationsDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('classifications.index')}}">Alle Klassifizierungen</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

