@component('mail::message')
# Hallo {{$camp->user['username']}}, Du hast einen Kurs erstellt.

Viel Erfolg bei deinem Kurs {{$camp['name']}} ({{$camp->camp_type['name']}}) für die Region {{$camp->group['name']}}. Solltest du noch Fragen haben, dann schaue auf dem Wiki oder melde dich bei
<a href="mailto:{{config('mail.from.address')}}">uns.</a>

@component('mail::button', ['url' => 'https://github.com/cevi/qualifikation/wiki'])
    Wiki
@endcomponent

Vielen Dank und viel Spass,<br>
Das CeviTools-Team
@endcomponent
