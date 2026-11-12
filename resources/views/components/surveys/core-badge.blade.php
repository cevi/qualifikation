{{-- Kernkompetenz: Etikett mit Info-Zeichen, Antippen zeigt die Beschreibung (tooltips.js, Text nie als HTML) --}}
@props(['text', 'short' => false])
<button type="button" data-tooltip="{{ $text }}" {{ $attributes->class([
    'inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-50 py-0.5 ps-2 pe-1.5 font-semibold text-blue-800 hover:bg-blue-100 focus:outline-hidden focus:ring-2 focus:ring-blue-300 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 dark:focus:ring-blue-700',
    $short ? 'text-[11px]' : 'text-xs',
]) }}>
    <svg class="size-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 1l1.5 3.2 3.5.4-2.6 2.4.7 3.5L6 8.8 2.9 10.5l.7-3.5L1 4.6l3.5-.4z"/></svg>
    {{ $short ? 'Kern' : 'Kernkompetenz' }}<span class="sr-only">: Beschreibung anzeigen</span>
    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><circle cx="7" cy="7" r="5.75"/><path d="M7 6.3v3.5M7 4.3v.1"/></svg>
</button>
