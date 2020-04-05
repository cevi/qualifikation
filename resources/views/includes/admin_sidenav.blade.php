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
            <h5 class="sidenav-heading">Zopfaktion</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">                  
                <li><a href="/admin"> <i class="fas fa-home"></i>Dashboard</a></li>
                @if ((isset(Auth::user()->camp) OR (Auth::user()->isAdmin())))
                    <li><a href="#UsersDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-users"></i>Teilnehmer</a>
                        <ul id="UsersDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('users.index')}}">Alle Teilnehmer</a>
                            </li>

                            <li>
                            <a href="{{route('users.create')}}">Teilnehmer erstellen</a>
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
                <li><a href="#CampsDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-campground"></i> Lager</a>
                    <ul id="CampsDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('camps.index')}}">Alle Lager</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                @if (Auth::user()->isCampleader())
                    <li><a href="#SurveysDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-poll-h"></i> Umfragen</a>
                        <ul id="SurveysDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('surveys.index')}}">Alle Umfragen</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                @endif
                @if (Auth::user()->isAdmin())
                    <li><a href="#QuestionsDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-question"></i> Fragen</a>
                        <ul id="QuestionsDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('questions.index')}}">Alle Fragen</a>
                            </li>
                            <li>
                                <a href="{{route('questions.create')}}">Frage erstellen</a>
                            </li>
                            <li>
                                <a href="{{route('chapters.index')}}">Kapitel</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li><a href="#AnswersDropdown" aria-expanded="false" data-toggle="collapse"><i class="fas fa-edit"></i> Antworten</a>
                        <ul id="AnswersDropdown" class="collapse list-unstyled ">
                            <li>
                                <a href="{{route('answers.index')}}">Alle Antworten</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

