@component('mail::message')
# Du hast einen neuen Kurs erstellt.

Viel Erfolg bei deinem Kurs. Solltest du noch Fragen haben, dann melde dich bei <a href="mailto:{{config('mail.from.address')}}">uns.</a>

{{--@component('mail::button', ['url' => ''])--}}
{{--Button Text--}}
{{--@endcomponent--}}

Vielen Dank und viel Spass,<br>
Das CeviTools-Team
@endcomponent