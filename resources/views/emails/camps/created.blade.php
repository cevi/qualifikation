@component('mail::message')
# Hallo {{ $camp->user['username'] }}

Du hast den Kurs **{{ $camp['name'] }}** ({{ $camp->camp_type['name'] }}) für die Region {{ $camp->group['name'] }} erstellt. Viel Erfolg!

Auf der Übersicht zeigt dir «Erste Schritte», was als Nächstes ansteht: Leitende und Teilnehmende importieren, zuteilen und die Qualifikationen erstellen.

@component('mail::button', ['url' => url('/home')])
Zur Übersicht
@endcomponent

Solltest du noch Fragen haben, dann schaue auf dem [Wiki](https://github.com/cevi/qualifikation/wiki) nach oder melde dich bei [uns](mailto:{{ config('mail.from.address') }}).

Vielen Dank und viel Spass,<br>
Das CeviTools-Team
@endcomponent
