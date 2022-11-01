@component('mail::message')
    # Neue Rückmeldung erhalten.

    {{$feedback->user['username']}} Hat eine Rückmeldung erfasst:
    {{$feedback->feedback}}

    Das CeviTools-Team
@endcomponent
