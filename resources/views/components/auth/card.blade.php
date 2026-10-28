{{-- Karte der Konto-Seiten (Registrieren, Passwort zurücksetzen, E-Mail bestätigen) --}}
@props(['title'])
<div class="mx-auto max-w-3xl">
    <div class="rounded-lg border border-gray-200 bg-white shadow-xs dark:border-gray-700 dark:bg-gray-800">
        <h1 class="m-0 border-b border-gray-200 px-5 py-3 text-base font-normal text-gray-600 dark:border-gray-700 dark:text-gray-300">{{ $title }}</h1>
        <div class="p-5">
            {{ $slot }}
        </div>
    </div>
</div>
