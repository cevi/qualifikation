<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        <div class="antialiased page">
            @include('includes/admin_topnav')
            <main class="p-4 md:ml-64 h-auto pt-20">
                @yield('content')
            </main>
            
            <x-footer/>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
        @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
        @stack('scripts')
    </body>
</html>