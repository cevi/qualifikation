{{-- Eine Zeile der Aufgabenliste (App\Services\CampChecks). level: danger = rot, warning = orange, normal = grau --}}
@props(['title', 'subtitle', 'level' => 'normal', 'link'])
@php
    [$dot, $text, $hint] = match ($level) {
        'danger' => ['bg-red-600 dark:bg-red-500', 'text-red-700 dark:text-red-400', 'dringend'],
        'warning' => ['bg-orange-500', 'text-orange-700 dark:text-orange-400', 'wichtig'],
        default => ['bg-gray-300 dark:bg-gray-500', 'text-gray-900 dark:text-white', null],
    };
@endphp
<li>
    <a href="{{ $link }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
        <span class="size-2.5 shrink-0 rounded-full {{ $dot }}" aria-hidden="true"></span>
        <span class="min-w-0 flex-1">
            <span class="block font-medium {{ $text }}">
                {{ $title }}@if ($hint)<span class="sr-only"> ({{ $hint }})</span>@endif
            </span>
            <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</span>
        </span>
        <i class="fas fa-chevron-right text-xs text-gray-400" aria-hidden="true"></i>
    </a>
</li>
