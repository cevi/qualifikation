{{--
    Bewertung als Marke (Vergleich, Hinweis für Leitende). Farbe nach Wert (answers.count): -1 orange, 0 grau, 1 hellblau,
    2 dunkelblau, wie im Radar. Antippen zeigt die Beschreibung (tooltips.js). Ohne Antwort: «offen».
--}}
@props(['answer' => null, 'small' => false])
@if ($answer)
    @php
        $tone = match ((int) $answer['count']) {
            -1 => 'bg-orange-100 text-orange-900 dark:bg-orange-900 dark:text-orange-100',
            1 => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            2 => 'bg-blue-700 text-white dark:bg-blue-500',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    @endphp
    <span data-tooltip="{{ $answer['description'] }}" {{ $attributes->class([
        'inline-flex cursor-help items-center justify-center rounded-md font-bold',
        $small ? 'h-5 min-w-6 px-1 text-xs' : 'h-6 min-w-8 px-1.5 text-sm',
        $tone,
    ]) }}>{{ $answer['name'] }}</span>
@else
    <span {{ $attributes->class(['text-xs text-gray-500 dark:text-gray-400']) }}>offen</span>
@endif
