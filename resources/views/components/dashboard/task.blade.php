{{-- Eine Zeile der Aufgabenliste (App\Services\CampChecks) --}}
@props(['title', 'subtitle', 'urgent' => false, 'link'])
<li>
    <a href="{{ $link }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
        <span @class(['size-2.5 shrink-0 rounded-full', 'bg-red-600 dark:bg-red-500' => $urgent, 'bg-gray-300 dark:bg-gray-500' => ! $urgent]) aria-hidden="true"></span>
        <span class="min-w-0 flex-1">
            <span @class(['block font-medium', 'text-red-700 dark:text-red-400' => $urgent, 'text-gray-900 dark:text-white' => ! $urgent])>
                {{ $title }}@if ($urgent)<span class="sr-only"> (dringend)</span>@endif
            </span>
            <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</span>
        </span>
        <i class="fas fa-chevron-right text-xs text-gray-400" aria-hidden="true"></i>
    </a>
</li>
