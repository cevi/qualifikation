{{-- Kopf von Quali und Vergleich: Art der Seite klein, Name der TN-Person gross, rechts Status und Aktionen (Slot) --}}
@props(['survey', 'eyebrow', 'help' => null])
@php
    $user = $survey->campUser->user;
    $group = $user->group['shortname'] ?? '';
@endphp
<header class="mt-4 flex flex-wrap items-end justify-between gap-x-6 gap-y-3 pb-4">
    <div class="min-w-0">
        <p class="mb-1 text-xs font-semibold tracking-wide text-gray-600 uppercase dark:text-gray-400">{{ $eyebrow }}</p>
        <h1 class="text-2xl leading-tight font-bold md:text-3xl dark:text-white">
            {{ $user->username }}
            @if ($group !== '')
                <span class="text-base font-medium text-gray-500 md:text-lg dark:text-gray-400">{{ $group }}</span>
            @endif
            @if (isset($help['content']))
                <x-layouts.help-button/>
            @endif
        </h1>
        <x-surveys.status-badge :survey="$survey" class="mt-2 inline-block"/>
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</header>
@if (isset($help['content']))
    <x-layouts.drawer-content :help="$help"/>
@endif
