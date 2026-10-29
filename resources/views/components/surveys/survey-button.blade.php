{{-- Knopf der Qualifikationsliste; ohne Qualis deaktiviert. variant: purple (Export) oder secondary --}}
@props([
    'text',
    'hasSurveys',
    'url' => 'javascript:;',
    'btnClass' => 'opensurvey',
    'target' => '_self',
    'variant' => 'secondary',
])

@php($buttonClass = $variant === 'purple' ? 'button-purple' : 'button-secondary')
@if($hasSurveys)
    <a href="{{ $url }}" target="{{ $target }}" class="{{ $buttonClass }} {{ $btnClass }}" role="button">{{ $text }}</a>
@else
    <span title="Bitte zuerst auf 'Qualifikationen erstellen' klicken" class="inline-block cursor-not-allowed">
        <button disabled class="{{ $buttonClass }} pointer-events-none opacity-50">{{ $text }}</button>
    </span>
@endif
