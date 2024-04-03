<!DOCTYPE html>
<html lang="{{ app()->getLocale()}}">
    @include('includes/header')
    <body class="text-dark__black">
        <div id="app" class="antialiased page mainpage">
            @include('includes/topnav')
            <main class="p-4 md:ml-64 h-auto pt-20">
                @yield('content')
            </main>
            <x-footer/>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
        @stack('scripts')
    </body>
</html>