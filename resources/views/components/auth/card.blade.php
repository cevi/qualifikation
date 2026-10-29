{{-- Karte der Konto-Seiten (Registrieren, Passwort zurücksetzen, E-Mail bestätigen) --}}
@props(['title'])
<div class="mx-auto max-w-3xl">
    <div class="panel">
        <h1 class="panel-header m-0 text-base font-normal text-gray-600 dark:text-gray-300">{{ $title }}</h1>
        <div class="panel-body">
            {{ $slot }}
        </div>
    </div>
</div>
