@extends('layouts.layout')

{{--
    Vergleich: pro Kompetenz eine Spalte je Einschätzung, der Kommentar steht unter der Bewertung. Die Spalte der
    Leitenden, ihre Bemerkung und die Rückmeldungen sehen nur Leitende. Auf dem Handy wird jede Zeile eine Karte.
    Zelle der Leitenden wie ihre Linie im Radar: oranger Rand, leichte Füllung (Helper::GetDataset = orange-600,
    dunkel radar.js DARK_SERIES = orange-400).
--}}
@section('survey_content')
    @foreach ($surveys as $survey)
        @php
            $isParticipant = $aktUser->isTeilnehmer();
            $columns = [['label' => '1. Selbsteinschätzung', 'short' => '1. Selbst', 'answer' => 'answer_first', 'comment' => 'comment_first', 'leader' => false]];
            if ($camp['secondsurveyopen']) {
                $columns[] = ['label' => '2. Selbsteinschätzung', 'short' => '2. Selbst', 'answer' => 'answer_second', 'comment' => 'comment_second', 'leader' => false];
            }
            if (! $isParticipant) {
                $columns[] = ['label' => 'Leitende', 'short' => 'Leitende', 'answer' => 'answer_leader', 'comment' => 'comment_leader', 'leader' => true];
            }
            // Feste Klassen, damit Tailwind sie findet
            $grid = match (count($columns)) {
                1 => 'md:grid-cols-[2.75rem_minmax(0,1fr)_15rem]',
                2 => 'md:grid-cols-[2.75rem_minmax(0,1fr)_repeat(2,14rem)]',
                default => 'md:grid-cols-[2.75rem_minmax(0,1fr)_repeat(3,12.5rem)]',
            };
            // Abweichung nur für Leitende (Regel in SurveyQuestion::differsFromLeader, gleich wie in der Druckansicht)
            $differs = fn ($question) => ! $isParticipant && $question->differsFromLeader((bool) $camp['secondsurveyopen']);
            $differences = $survey->chapters->sum(fn ($chapter) => $chapter->questions->filter($differs)->count());
            $canFinish = $survey->campUser->isLedBy($aktUser) && $survey->status() === \App\Enums\SurveyStatus::TnAbgeschlossen;
            $finished = $survey->campUser->isLedBy($aktUser) && $survey->status() === \App\Enums\SurveyStatus::Fertig;
            $lock = '<svg class="size-3 shrink-0" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.5" y="6" width="9" height="6.5" rx="1.2"/><path d="M4.5 6V4.2a2.5 2.5 0 0 1 5 0V6"/></svg>';
        @endphp

        <x-surveys.page-header :survey="$survey" :help="$help" eyebrow="Vergleich">
            @unless ($isParticipant)
                <a href="{{ route('survey.downloadPDF', $survey) }}" class="button-small">
                    <svg class="size-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4.5 6V2.5h7V6M4.5 11.5H3A1.5 1.5 0 0 1 1.5 10V7.5A1.5 1.5 0 0 1 3 6h10a1.5 1.5 0 0 1 1.5 1.5V10a1.5 1.5 0 0 1-1.5 1.5h-1.5"/><rect x="4.5" y="9.5" width="7" height="4.5"/></svg>
                    Druckansicht
                </a>
            @endunless
            @if ($survey->TNIsAllowed())
                <a href="{{ route('survey.survey', $survey->slug) }}" class="button-small">Quali bearbeiten</a>
            @endif
            {{-- Wie SurveysController@finish: abschliessen geht nur, wenn die TN-Person fertig ist, und nur einmal. --}}
            @if ($canFinish)
                <x-forms.form method="PATCH" :model="$survey" :action="action('SurveysController@finish', $survey->id)">
                    <button type="submit" class="button-small-primary">
                        <svg class="size-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8.5 6.5 12 13 4.5"/></svg>
                        Qualifikationsprozess abschliessen
                    </button>
                </x-forms.form>
            @elseif ($finished)
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">
                    <svg class="size-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8.5 6.5 12 13 4.5"/></svg>
                    Qualifikationsprozess abgeschlossen
                </span>
            @endif
        </x-surveys.page-header>

        <div class="group/vergleich">
            <div class="mb-4 flex flex-wrap items-center gap-x-5 gap-y-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 dark:border-gray-700 dark:bg-gray-800">
                <x-surveys.bewertungs-schluessel :answers="$answers" class="flex-1"/>
                @unless ($isParticipant)
                    <p class="mb-0 flex w-full flex-wrap items-center gap-x-5 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">{!! $lock !!} Spalte «Leitende» und Bemerkung sehen die Teilnehmenden nicht.</span>
                        @if ($differences > 0)
                            <label class="inline-flex items-center gap-1.5 font-medium text-gray-900 md:ms-auto dark:text-white">
                                <input type="checkbox" id="nur-abweichungen" class="field-check">
                                Nur Abweichungen ({{ $differences }})
                            </label>
                        @endif
                    </p>
                @endunless
            </div>

            @foreach ($survey->chapters as $chapter)
                @php
                    $chapterDiffers = $chapter->questions->contains($differs);
                @endphp
                <details open @class([
                    'group/kapitel mb-4 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800',
                    'group-has-[#nur-abweichungen:checked]/vergleich:hidden' => ! $chapterDiffers,
                ])>
                    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-3 md:px-5 [&::-webkit-details-marker]:hidden">
                        <h2 class="m-0 text-base font-bold text-gray-900 md:text-lg dark:text-white">{{ $chapter->chapter['number'] }}. {{ $chapter->chapter['name'] }}</h2>
                        <svg class="ms-auto size-5 shrink-0 text-gray-500 transition-transform group-open/kapitel:rotate-180" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 8l5 5 5-5"/></svg>
                    </summary>
                    <div class="hidden gap-x-5 border-y border-gray-200 bg-gray-50 px-5 py-2 text-xs font-semibold tracking-wide text-gray-700 uppercase md:grid {{ $grid }} dark:border-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                        <span>Nr.</span>
                        <span>Kompetenz</span>
                        @foreach ($columns as $column)
                            <span @class(['inline-flex items-center gap-1.5', 'text-orange-600 dark:text-orange-400' => $column['leader']])>@if ($column['leader']){!! $lock !!}@endif{{ $column['label'] }}</span>
                        @endforeach
                    </div>
                    <div class="divide-y divide-gray-100 max-md:border-t max-md:border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                        @foreach ($chapter->questions as $question)
                            @php
                                $info = $question->competence_text();
                                $number = $question->question['number'];
                                $diff = $differs($question);
                            @endphp
                            <div @class([
                                'px-4 py-3 md:grid md:items-start md:gap-x-5 md:px-5',
                                $grid,
                                'group-has-[#nur-abweichungen:checked]/vergleich:hidden' => ! $diff,
                            ]) @if ($diff) data-differs @endif>
                                <span class="hidden pt-0.5 text-sm font-bold text-gray-700 md:block dark:text-gray-300">{{ $number }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <span class="rounded-md bg-gray-100 px-1.5 py-0.5 text-sm font-bold text-gray-700 md:hidden dark:bg-gray-700 dark:text-gray-200">{{ $number }}</span>
                                        <h3 class="m-0 text-sm font-semibold text-gray-900 dark:text-white">{{ $question->question['competence'] }}</h3>
                                        @if ($info)
                                            <x-surveys.core-badge :text="$info" :short="true"/>
                                        @endif
                                        @if ($diff)
                                            <span class="inline-flex items-center rounded-full border border-orange-400 px-1.5 py-px text-[11px] font-semibold text-orange-900 dark:border-orange-400 dark:text-orange-400">Abweichung</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 mb-0 text-xs text-gray-600 dark:text-gray-400">{{ $question->question['name'] }}</p>
                                </div>
                                @foreach ($columns as $column)
                                    <div @class([
                                        'mt-2 flex gap-3 md:mt-0 md:block',
                                        'md:-mx-2 md:-my-1.5 md:rounded-md md:border md:border-orange-600 md:bg-orange-600/8 md:px-2 md:py-1.5 dark:md:border-orange-400 dark:md:bg-orange-400/12' => $column['leader'],
                                    ])>
                                        <span @class([
                                            'inline-flex w-24 shrink-0 items-start gap-1 pt-0.5 text-xs md:hidden',
                                            'font-semibold text-orange-600 dark:text-orange-400' => $column['leader'],
                                            'text-gray-600 dark:text-gray-400' => ! $column['leader'],
                                        ])>@if ($column['leader']){!! $lock !!}@endif{{ $column['short'] }}</span>
                                        <div class="min-w-0">
                                            <x-surveys.rating-chip :answer="$question->{$column['answer']}"/>
                                            @if (filled($question[$column['comment']]))
                                                <p class="mt-1 mb-0 text-xs text-gray-700 dark:text-gray-300">{!! nl2br(e($question[$column['comment']])) !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>

        @unless ($isParticipant)
            <section class="mb-4 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between gap-2 border-b border-gray-200 px-4 py-3 md:px-5 dark:border-gray-700">
                    <h2 class="m-0 text-base font-bold text-gray-900 md:text-lg dark:text-white">Bemerkung</h2>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400">{!! $lock !!} nur Leitende</span>
                </div>
                <div class="px-4 py-3 text-sm md:px-5 dark:text-gray-200">
                    @if (filled($survey['comment']))
                        {!! nl2br(e($survey['comment'])) !!}
                    @else
                        <span class="text-gray-500 dark:text-gray-400">Noch keine Bemerkung.</span>
                    @endif
                </div>
            </section>
        @endunless
        @if ($aktUser->isLeader() || $aktUser->isCampleader())
            <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :editable="false" :card="true"/>
        @endif
        <x-surveys.radar-chart/>
    @endforeach
@endsection

@push('scripts')
    @vite('resources/js/pages/tooltips.js')
    @include('home.radar')
@endpush
