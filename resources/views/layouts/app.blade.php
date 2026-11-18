<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        {{-- focus:[clip:auto]: Font Awesomes .sr-only setzt das alte clip, das not-sr-only nicht zurücksetzt --}}
        <a href="#inhalt" class="sr-only focus:not-sr-only focus:[clip:auto] focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-blue-700 focus:shadow-lg dark:focus:bg-gray-800 dark:focus:text-blue-400">Zum Inhalt springen</a>
        @include('includes/topnav')
        <div id="app" class="antialiased page @auth max-md:pb-[calc(9rem+env(safe-area-inset-bottom))]! @endauth">
            <main id="inhalt" tabindex="-1" class="p-4 h-auto pt-20 scroll-mt-20 focus:outline-hidden">
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