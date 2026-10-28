{{-- Status einer Quali, überall gleiche Farben: Neu grau, offen blau, wartet auf Leitende orange, fertig grün --}}
@props(['survey'])
@php
    $classes = match ($survey->status()) {
        \App\Enums\SurveyStatus::Neu => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        \App\Enums\SurveyStatus::ErsteRundeOffen, \App\Enums\SurveyStatus::ZweiteRundeOffen => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        \App\Enums\SurveyStatus::TnAbgeschlossen => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        \App\Enums\SurveyStatus::Fertig => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    };
@endphp
<span {{ $attributes->class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap', $classes]) }}>{{ $survey->survey_status['name'] }}</span>
