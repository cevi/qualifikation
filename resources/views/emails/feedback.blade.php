@component('mail::message')
# Neue Rückmeldung zum Qualitool

{{ $feedback->user['username'] }} hat eine Rückmeldung erfasst:

{{-- Text aus dem Formular: maskiert und als ein HTML-Block auf einer Zeile, sonst liest Markdown Listen, Links und Einrückungen daraus --}}
<p>{!! str_replace(["\r\n", "\r", "\n"], '<br>', e($feedback->feedback)) !!}</p>

Das CeviTools-Team
@endcomponent
