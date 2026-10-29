{{-- Radar-Diagramme (resources/js/pages/radar.js); Leinwände #radarChart-1, -2 … in der Seite --}}
<script type="application/json" id="radar-config">@json(['labels' => $labels, 'datasets' => $datasets])</script>
@vite('resources/js/pages/radar.js')
