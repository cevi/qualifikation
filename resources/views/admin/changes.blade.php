@extends('layouts.admin')

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="feedback-wrapper" class="card feedback">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#feedback-wrapper" href="#feedback-box" aria-expanded="true" aria-controls="feedback-box">Rückmeldungen</a></h2><a data-toggle="collapse" data-parent="#feedback-wrapper" href="#feedback-box" aria-expanded="true" aria-controls="feedback-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="feedback-box" role="tabpanel" class="card-body collapse show">
                    @if (session()->has('success'))
                        <div class="alert alert-dismissable alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>
                                {!! session()->get('success') !!}
                            </strong>
                        </div>
                    @endif
                    {!! Form::open(['method' => 'POST', 'action'=>'FeedbackController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('feedback', 'Rückmeldung:') !!}
                        {!! Form::textarea('feedback', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Rückmeldung absenden', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-header section-padding section-features shadow">
        <div class="container-fluid">
            <div id="features-wrapper" class="card features">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display"><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box">Änderungen und Anpassungen</a></h2><a data-toggle="collapse" data-parent="#features-wrapper" href="#features-box" aria-expanded="true" aria-controls="features-box"><i class="fa fa-angle-down"></i></a>
                </div>
                <div id="features-box" role="tabpanel" class="card-body collapse show">
                    <h3>V6.2</h3>
                    <ul>
                        <li>Eigene Posts können bearbeitet werden.</li>
                        <li>Mail wird versendet bei neuem Kurs.</li>
                    </ul>
                    <h3>V6.1</h3>
                    <ul>
                        <li>Löschen der Kurse aktualisiert Zähler auf der Homepage.</li>
                    </ul>
                    <h3>V6</h3>
                    <ul>
                        <li>Login durch Cevi-DB Benutzerdaten.</li>
                    </ul>
                    <h3>V5</h3>
                    <ul>
                        <li>Eigene Kurs-Erstellung ermöglichen.</li>
                        <li>Landing Page mit Screenshots, Anleitung, etc.</li>
                        <li>Mobile-Tauglichkeit weiter verbessern.</li>
                    </ul>
                    <h3>V4</h3>
                    <ul>
                        <li>Eigene Kommentare löschen auf Teilnehmerprofilen.</li>
                        <li>Verbesserte Fehlermeldung bei Importproblemen aus der Cevi-DB.</li>
                        <li>Mobile-Tauglichkeit verbessern. Vor allem für eine schnelle Erfassung von Beobachtungen.</li>
                        <li>Ans Cevi-CI anpassen, damit es offizieller wirkt / Schriften gemäß RV</li>
                        <li>Von der Qualifikation eine optimierte Druckansicht erstellen, während dem Gespräch ist Papier wohl oft angenehmer als ein Bildschirm (Diagramm auf ganzes A4 skalieren)</li>
                        <li>Meldung einbauen, damit für die Leitenden ersichtlich ist, dass TNs die Bewertung und Kommentare nicht sehen können.</li>
                        <li>Upload von Dateien auf dem Teilnehmerprofil (Rückmeldungen zu Programm- und Sportblöcken)</li>
                        <li>Passwort selber über hinterlegte Mailadresse zurücksetzen können</li>
                        <li>Kernkompetenzen als Anhangstabellen an Fragen, für mehrere Kernkompetenzen bei verschiedenen Kurstypen</li>
                        <li>Expertenkurs als Kurstyp registriert.</li>
                        <li>Im Quali-Bogen keine Tooltips. Auf Mobilgeräten erscheinen diese z.T. nicht (Erklärung - 0 + ++ und Schlüsselkompetenzen)</li>
                    </ul>
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
                </div>
            </div>
        </div>
    </section>

@endsection
