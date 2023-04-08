@extends('layouts.admin')

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Rückmeldungen</h5>
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
    </section>

    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Änderungen
            und Anpassungen</h5>
        <ol class="relative border-l border-gray-200 dark:border-gray-700">
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V6.4</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Möglichkeit, das Ausfüllen der Qualifikationen im Menüpunkt "Qualifikationen" zu steuern. <br>
                    Ansicht der ersten und zweiten Selbsteinschätzung überarbeitet. <br>
                    Eigene Kurs-Typen erstellen mit persönlichen Kapitel, Fragen und Kernkompetenzen.
                </p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V6.3</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Dark Mode <br>
                    Kernkompetenzen fett darstellen in Spinne. <br>
                    Spinne aktualisieren auf Änderung.
                </p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V6.2</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Eigene Posts können bearbeitet werden. <br>
                    Mail wird versendet bei neuem Kurs.</p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V6.1</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">Löschen der Kurse aktualisiert
                    Zähler auf der Homepage.</p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V6</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Login durch Cevi-DB Benutzerdaten.</p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V5</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Eigene Kurs-Erstellung ermöglichen. <br>
                    Landing Page mit Screenshots, Anleitung, etc. <br>
                    Mobile-Tauglichkeit weiter verbessern.</p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V4</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Eigene Kommentare löschen auf Teilnehmerprofilen. <br>
                    Verbesserte Fehlermeldung bei Importproblemen aus der Cevi-DB. <br>
                    Mobile-Tauglichkeit verbessern. Vor allem für eine schnelle Erfassung von Beobachtungen. <br>
                    Ans Cevi-CI anpassen, damit es offizieller wirkt / Schriften gemäß RV. <br>
                    Von der Qualifikation eine optimierte Druckansicht erstellen, während dem Gespräch ist
                    Papier wohl oft angenehmer als ein Bildschirm (Diagramm auf ganzes A4 skalieren). <br>
                    Meldung einbauen, damit für die Leitenden ersichtlich ist, dass TNs die Bewertung und
                    Kommentare nicht sehen können. <br>
                    Upload von Dateien auf dem Teilnehmerprofil (Rückmeldungen zu Programm- und Sportblöcken). <br>
                    Passwort selber über hinterlegte Mailadresse zurücksetzen können. <br>
                    Kernkompetenzen als Anhangstabellen an Fragen, für mehrere Kernkompetenzen bei verschiedenen
                    Kurstypen. <br>
                    Expertenkurs als Kurstyp registriert. <br>
                    Im Quali-Bogen keine Tooltips. Auf Mobilgeräten erscheinen diese z.T. nicht (Erklärung - 0 +
                    ++ und Schlüsselkompetenzen) <br>

                </p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V3</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Die 2. Selbsteinschätzung wird für alle Teilnehmer unter dem Menüpunkt Qualifikationen
                    freigeschalten. <br>
                    Beim Bearbeiten des Qualifikations-Status können mittels Flag die bisherigen Antworten
                    wieder zurückgesetzt werden.

                </p>
            </li>
        </ol>
    </section>
    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Geplante Änderungen</h5>
        <ol class="relative border-l border-gray-200 dark:border-gray-700">

        </ol>
    </section>

@endsection
