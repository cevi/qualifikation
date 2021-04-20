@extends('layouts.admin')

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="features-wrapper" class="card features">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box">Änderungen und Anpassungen</a></h2><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="features-box" role="tabpanel" class="card-body collapse show">
                    <h3>V3.1</h3>
                    <ul>
                        <li>Die 2. Selbsteinschätzung wird für alle Teilnehmer unter dem Menüpunkt Qualifikationen freigeschalten.</li>
                        <li>Beim Bearbeiten des Qualifikations-Status können mittels Flag die bisherigen Antworten wieder zurückgesetzt werden.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="roadmap-wrapper" class="card roadmap">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#roadmap-wrapper" href="#roadmap-box" aria-expanded="true" aria-controls="roadmap-box">Geplante Änderungen</a></h2><a data-toggle="collapse" data-parent="#roadmap-wrapper" href="#roadmap-box" aria-expanded="true" aria-controls="roadmap-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="roadmap-box" role="tabpanel" class="card-body collapse show">
                    <h3>V4</h3>
                    <ul>
                        <li>Eigene Kommentare löschen auf Teilnehmerprofilen.</li>
                        <li>Verbesserte Fehlermeldung bei Importproblemen aus der Cevi-DB.</li>
                    </ul>
                    <h3>V5</h3>
                    <ul>
                        <li>Login durch Cevi-DB Benutzerdaten.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

@endsection