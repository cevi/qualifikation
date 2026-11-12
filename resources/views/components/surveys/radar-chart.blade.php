<div class="panel">
    <div class="panel-header">
        <h2 class="m-0 min-w-0 text-base font-bold text-gray-900 md:text-lg dark:text-white">Kompetenzendarstellung {{$name}}</h2>
    </div>
    {{-- Eigene Abstände statt .panel-body (p-5): wenig Rand, damit das Diagramm den Platz der Karte nutzt --}}
    <div class="px-1 py-3 sm:px-4 sm:py-2">
        <div class="mx-auto w-[1000px] max-w-full">
            <canvas id="radarChart-{{$id}}" width="100%" height="100%"></canvas>
        </div>
    </div>
</div>
