<nav class="navbar navbar-expand-md shadow-sm border-gray-200">
    <div class="container-fluid">
        <div class="navbar-holder d-flex align-items-center justify-content-between">
            <div class="navbar-nav navbar-header">
                <a id="toggle-btn" href="#" class="menu-btn">
                    <i class="fas fa-bars"></i> </a>
            </div>
            <div class="navbar-nav navbar-header">
                <a href="{{ url('/admin') }}">
                    <img src="/img/logo.svg" alt="..." style="width: 20rem" class="img-fluid">
                </a>
            </div>
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                <x-camp-dropdown/>
                <x-user-dropdown/>

            </ul>
        </div>
    </div>
    <x-toggle-switch/>
</nav>
