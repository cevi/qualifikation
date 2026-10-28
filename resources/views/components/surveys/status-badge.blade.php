{{-- Status einer Quali, überall gleiche Farben (SurveyStatus::tone()): Neu grau, offen blau, wartet auf Leitende orange, fertig grün --}}
@props(['survey'])
@php
    // Gleiche Klassen wie der Status in der Quali-Liste (TONES in resources/js/components/DataTableCell.js)
    $classes = match ($survey->status()->tone()) {
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    };
@endphp
<span {{ $attributes->class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap', $classes]) }}>{{ $survey->survey_status['name'] }}</span>
