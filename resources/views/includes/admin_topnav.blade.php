<!-- navbar-->
<div class="page">
    <header class="header">
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                <div class="navbar-header"><a id="toggle-btn" href="#" class="menu-btn"><i class="fas fa-bars"></i> </a><a href="/admin" class="navbar-brand">
                    <div class="brand-text d-none d-md-inline-block"><span>Qualifikations </span><strong class="text-primary">Dashboard</strong></div></a></div>
                <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                    <!-- Log out-->
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
                            <span class="d-none d-sm-inline-block">Logout</span> <i class="fas fa-sign-out-alt"></i>
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