{{-- Meldung (Flowbite): type success oder danger; mit id lässt sie sich über das Kreuz schliessen (Flowbite data-dismiss-target). --}}
@props(['type' => 'success', 'id' => null])
@php
    $colors = [
        'success' => 'text-green-800 bg-green-50 dark:bg-gray-800 dark:text-green-400',
        'danger' => 'text-red-800 bg-red-50 dark:bg-gray-800 dark:text-red-400',
    ][$type];
@endphp
<div @if($id) id="{{ $id }}" @endif role="alert" {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 mb-4 text-sm rounded-lg $colors"]) }}>
    <div class="min-w-0 flex-1">{{ $slot }}</div>
    @if($id)
        <button type="button" data-dismiss-target="#{{ $id }}" aria-label="Schliessen"
                class="-m-1.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg hover:bg-black/5 dark:hover:bg-white/10">
            <span aria-hidden="true" class="text-lg leading-none">&times;</span>
        </button>
    @endif
</div>
