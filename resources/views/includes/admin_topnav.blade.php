<!-- navbar-->
<div class="page">
    <header class="header">
        <nav class="navbar navbar-dashboard">
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                    <div class="navbar-nav navbar-header"><a id="toggle-btn" href="#" class="menu-btn"><i class="fas fa-bars"></i> </a><a href="/admin" class="navbar-brand">
                        <div class="nav-link nav-item brand-text d-none d-md-inline-block"><span>Qualifikations </span><strong class="badge-primary-light">Dashboard</strong></div></a></div>
                    <ul class="navbar-nav d-flex flex-md-row align-items-md-center">
                        <!-- Log out-->
                        <li>
                            <a href="{{ route('logout') }}" class="nav-link nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
                                Logout <i class="fas fa-sign-out-alt"></i>
                            </a> 
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>