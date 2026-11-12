<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        <a href="#inhalt" class="sr-only focus:not-sr-only focus:[clip:auto] focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-blue-700 focus:shadow-lg dark:focus:bg-gray-800 dark:focus:text-blue-400">Zum Inhalt springen</a>
        {{-- Ohne Menüs, wenn Migrationen offen sind (Wartung): Sie fragen Kursdaten ab, deren Tabellen noch fehlen können. --}}
        @unless ($withoutNav ?? false)
            @include('includes/topnav')
        @endunless
        <div class="antialiased page @auth max-md:pb-[calc(9rem+env(safe-area-inset-bottom))]! @endauth">
            <main id="inhalt" tabindex="-1" class="p-4 h-auto pt-20 scroll-mt-20 focus:outline-hidden">
                @yield('content')
            </main>
            <x-layouts.footer/>
        </div>
        @auth
            @unless ($withoutNav ?? false)
                @include('includes.bottomnav')
            @endunless
        @endauth
        @stack('scripts')
    </body>
</html>