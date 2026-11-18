{{-- Bewertungsschlüssel als eine Zeile: Marke und erster Satz der Beschreibung, die ganze Beschreibung beim Antippen --}}
<div {{ $attributes->class(['flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-700 dark:text-gray-300']) }}>
    <span class="font-bold">Bewertung</span>
    @foreach ($answers as $answer)
        <span class="inline-flex items-center gap-1.5">
            <x-surveys.rating-chip :answer="$answer"/>
            {{ \Illuminate\Support\Str::before($answer['description'], '.') }}
        </span>
    @endforeach
    <span class="inline-flex items-center gap-1.5 md:ms-auto">
        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
            <svg class="size-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 1l1.5 3.2 3.5.4-2.6 2.4.7 3.5L6 8.8 2.9 10.5l.7-3.5L1 4.6l3.5-.4z"/></svg>Kernkompetenz
        </span>
        dieser Ausbildungsstufe
    </span>
</div>
