<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        @include('includes/topnav')
        <div id="app" class="antialiased page @auth max-md:pb-[calc(9rem+env(safe-area-inset-bottom))]! @endauth">
            <main class="p-4 h-auto pt-20">
                @yield('content')
            </main>
            <x-layouts.footer/>
        </div>
        @auth
            @include('includes.bottomnav')
        @endauth
        @stack('scripts')
    </body>
</html>