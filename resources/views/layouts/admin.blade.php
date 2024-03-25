<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        <div class="page">
            @include('includes/admin_topnav')
            @include('includes/admin_sidenav')

            <main class="py-4">
                @yield('content')
            </main>

            <x-footer/>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
        @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
        @stack('scripts')
    </body>
</html>
