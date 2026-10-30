<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('includes/header')
    <body class="text-dark__black">
        {{-- Ohne Menüs, wenn Migrationen offen sind (Wartung): Sie fragen Kursdaten ab, deren Tabellen noch fehlen können. --}}
        @unless ($withoutNav ?? false)
            @include('includes/topnav')
        @endunless
        <div class="antialiased page @auth max-md:pb-[calc(9rem+env(safe-area-inset-bottom))]! @endauth">
            <main class="p-4 h-auto pt-20">
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