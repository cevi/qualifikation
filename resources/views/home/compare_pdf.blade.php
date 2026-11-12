<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Qualifikation">
    <meta name="author" content="Jérôme Sigg v/o Amigo">
    <meta name="robots" content="all,follow">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">

    <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

    <!-- Styles -->
    @vite([
        'resources/css/legacy.css',
        'resources/css/app.css',
        'resources/js/app.js'])
</head>

{{--
    Druckansicht einer oder aller Qualis (SurveysController@downloadPDF, AdminSurveysController@downloadPDF). Aufbau
    wie der Vergleich, aber als festes Raster mit allen drei Einschätzungen, eine Quali pro Seite. Farben der Marken
    und der Leitenden-Spalte werden mitgedruckt (print-color-adjust). Papier: A3 aus legacy-base.css.
--}}
<body class="bg-white text-gray-900 [print-color-adjust:exact] [-webkit-print-color-adjust:exact]">
@php
    $lock = '<svg class="size-3 shrink-0" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.5" y="6" width="9" height="6.5" rx="1.2"/><path d="M4.5 6V4.2a2.5 2.5 0 0 1 5 0V6"/></svg>';
    $columns = [
        ['label' => '1. Selbsteinschätzung', 'answer' => 'answer_first', 'comment' => 'comment_first', 'leader' => false],
        ['label' => '2. Selbsteinschätzung', 'answer' => 'answer_second', 'comment' => 'comment_second', 'leader' => false],
        ['label' => 'Leitende', 'answer' => 'answer_leader', 'comment' => 'comment_leader', 'leader' => true],
    ];
    $answers = \App\Models\Answer::all();
    $grid = 'grid grid-cols-[2.75rem_minmax(0,1fr)_repeat(3,13rem)] items-start gap-x-5';
    // Abweichung wie im Vergleich: Leitende und letzte Selbsteinschätzung mindestens zwei Stufen auseinander
    $differs = function ($question) {
        $self = $question->answer_second ?? $question->answer_first;

        return $self && $question->answer_leader && abs($question->answer_leader['count'] - $self['count']) >= 2;
    };
@endphp
<main class="mx-auto max-w-[1200px] px-[15px] py-6 md:px-8">
    @foreach ($surveys as $survey)
        <article @class(['break-after-page' => ! $loop->last, 'mb-10' => ! $loop->last])>
            <header class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2 border-b border-gray-200 pb-4">
                <div class="min-w-0">
                    <p class="mb-1 text-xs font-semibold tracking-wide text-gray-600 uppercase">Qualifikation{{ isset($camp) ? ' · '.$camp->name : '' }}</p>
                    <h1 class="text-3xl leading-tight font-bold text-gray-900">
                        {{ $survey->campuser->user['username'] }}
                        @if (filled($survey->campuser->user->group['shortname'] ?? null))
                            <span class="text-lg font-medium text-gray-500">{{ $survey->campuser->user->group['shortname'] }}</span>
                        @endif
                    </h1>
                    <x-surveys.status-badge :survey="$survey" class="mt-2 inline-block"/>
                </div>
                <dl class="grid grid-cols-[auto_auto] gap-x-3 gap-y-0.5 text-sm">
                    <dt class="text-gray-600">Leitende</dt>
                    <dd class="m-0 font-medium">{{ $survey->campuser->leaderNames() ?: '–' }}</dd>
                    <dt class="text-gray-600">Stand</dt>
                    <dd class="m-0 font-medium">{{ now()->isoFormat('L') }}</dd>
                </dl>
            </header>

            <x-surveys.bewertungs-schluessel :answers="$answers" class="my-4"/>

            @foreach ($survey->chapters as $chapter)
                <section class="mb-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
                    <h2 class="m-0 break-after-avoid px-5 py-3 text-lg font-bold text-gray-900">{{ $chapter->chapter['number'] }}. {{ $chapter->chapter['name'] }}</h2>
                    <div class="{{ $grid }} border-y border-gray-200 bg-gray-50 px-5 py-2 text-xs font-semibold tracking-wide text-gray-700 uppercase">
                        <span>Nr.</span>
                        <span>Kompetenz</span>
                        @foreach ($columns as $column)
                            <span @class(['inline-flex items-center gap-1.5', 'text-orange-600' => $column['leader']])>@if ($column['leader']){!! $lock !!}@endif{{ $column['label'] }}</span>
                        @endforeach
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($chapter->questions as $question)
                            <div class="{{ $grid }} break-inside-avoid px-5 py-3">
                                <span class="pt-0.5 text-sm font-bold text-gray-700">{{ $question->question['number'] }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <h3 class="m-0 text-sm font-semibold text-gray-900">{{ $question->question['competence'] }}</h3>
                                        @if ($question->competence_text())
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-800">
                                                <svg class="size-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 1l1.5 3.2 3.5.4-2.6 2.4.7 3.5L6 8.8 2.9 10.5l.7-3.5L1 4.6l3.5-.4z"/></svg>Kern
                                            </span>
                                        @endif
                                        @if ($differs($question))
                                            <span class="inline-flex items-center rounded-full border border-orange-400 px-1.5 py-px text-[11px] font-semibold text-orange-900">Abweichung</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 mb-0 text-xs text-gray-600">{{ $question->question['name'] }}</p>
                                </div>
                                @foreach ($columns as $column)
                                    <div @class([
                                        'min-w-0',
                                        '-mx-2 -my-1.5 rounded-md border border-orange-600 bg-orange-600/8 px-2 py-1.5' => $column['leader'],
                                    ])>
                                        <x-surveys.rating-chip :answer="$question->{$column['answer']}"/>
                                        @if (filled($question[$column['comment']]))
                                            <p class="mt-1 mb-0 text-xs text-gray-700">{!! nl2br(e($question[$column['comment']])) !!}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <section class="mb-4 break-inside-avoid rounded-lg border border-gray-200 bg-white">
                <div class="flex items-center justify-between gap-2 border-b border-gray-200 px-5 py-3">
                    <h2 class="m-0 text-lg font-bold text-gray-900">Bemerkung</h2>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-600">{!! $lock !!} nur Leitende</span>
                </div>
                <div class="px-5 py-3 text-sm">
                    @if (filled($survey['comment']))
                        {!! nl2br(e($survey['comment'])) !!}
                    @else
                        <span class="text-gray-500">Keine Bemerkung.</span>
                    @endif
                </div>
            </section>

            <div class="break-before-page break-inside-avoid">
                <x-surveys.radar-chart :id="$loop->iteration" :name="$survey->campuser->user['username']"/>
            </div>
        </article>
    @endforeach
</main>

@include('home.radar')

</body>

</html>
