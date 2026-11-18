@php
    // Namen kommen von Kursleitenden: maskieren, weil die Zeile mit {!! !!} als Markdown ausgegeben wird
    $label = fn ($camp) => '**'.e($camp->name).'**'.($camp->camp_type ? ' ('.e($camp->camp_type->name).')' : '');
    $date = fn ($camp) => \Illuminate\Support\Carbon::parse($camp->end_date)->format('d.m.Y');
    $single = $camps->count() === 1;
@endphp
@component('mail::message')
# Hallo {{ $user->username }}

@if ($single)
Dein Kurs {!! $label($camps->first()) !!} ist am {{ $date($camps->first()) }} zu Ende gegangen, ist im Qualitool aber noch offen.
@else
Diese Kurse sind zu Ende gegangen, im Qualitool aber noch offen:

@foreach ($camps as $camp)
- {!! $label($camp) !!}, Schlussdatum {{ $date($camp) }}
@endforeach
@endif

Bitte schliesse {{ $single ? 'den Kurs' : 'die Kurse' }} ab, sobald alle Qualifikationen besprochen sind. Beim Abschliessen werden die Qualifikationen, Rückmeldungen und hochgeladenen Dateien gelöscht, damit keine Personendaten länger als nötig gespeichert bleiben. Lade vorher herunter, was du behalten möchtest, zum Beispiel die Druckversion aller Qualifikationen.

So geht's: Wähle den Kurs oben rechts in der Kursauswahl, öffne «Kursadmin → Kurs», klicke auf den Kursnamen und dann auf «Kurs abschliessen / löschen».

@component('mail::button', ['url' => url('/home')])
Zum Qualitool
@endcomponent

Du erhältst diese Erinnerung jeweils Ende Monat, bis {{ $single ? 'der Kurs abgeschlossen ist' : 'alle Kurse abgeschlossen sind' }}. Solltest du noch Fragen haben, dann schaue auf dem [Wiki](https://github.com/cevi/qualifikation/wiki) nach oder melde dich bei [uns](mailto:{{ config('mail.from.address') }}).

Vielen Dank,<br>
Das CeviTools-Team
@endcomponent
