<div class="card radar-chart-example">
    <div class="card-header d-flex align-items-center">
        <h4>Kompetenzendarstellung</h4>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="radarChart-1" width="100%" height="100%"></canvas>
        </div>
    </div>
    @if($save)
        <div class="card-footer d-flex align-items-center">
            Die Grafik wird erst nach dem Speichern aktualisiert.
        </div>
    @endif
</div>