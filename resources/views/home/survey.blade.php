@extends('layouts.layout')

{{--
    Quali ausfüllen. Teilnehmende füllen die 1. bzw. 2. Selbsteinschätzung aus, Leitende ihre Einschätzung und die
    Bemerkung. Speichert sich selbst (survey-autosave.js); die Leiste oben springt zu den Kapiteln.
--}}
@section('survey_content')
    @foreach ($surveys as $survey)
        @php
            $isLeader = $aktUser->isLeader();
            // Welche Einschätzung die Person ausfüllt: Leitende ihre, Teilnehmende die 1. oder 2. Selbsteinschätzung.
            // Die Runde steckt in der ID der Radio-Knöpfe (Frage.Runde.NummerAntwort), radar.js liest sie dort.
            [$round, $answerField, $commentField] = match (true) {
                $isLeader => [3, 'answer_leader_id', 'comment_leader'],
                $survey->status()->isFirstRound() => [1, 'answer_first_id', 'comment_first'],
                default => [2, 'answer_second_id', 'comment_second'],
            };
            $closeConfirm = [
                'data-confirm' => $survey->surveyName().' abschliessen?',
                'data-confirm-text' => 'Danach kannst du deine Einschätzung nicht mehr ändern.',
            ];
        @endphp

        <x-surveys.page-header :survey="$survey" :help="$help" :eyebrow="$isLeader ? 'Qualifikation · Einschätzung der Leitenden' : $survey->surveyName()">
            @if ($isLeader)
                <a href="{{ route('survey.compare', $survey->slug) }}" class="button-small">
                    Zum Vergleich
                    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7h8M8 4l3 3-3 3"/></svg>
                </a>
            @endif
        </x-surveys.page-header>

        <x-forms.form method="PATCH" :model="$survey" :action="action('SurveysController@update', $survey->slug)" class="survey-form">
            {{-- Leiste: Kapitel, Speicherstand, Abschliessen. Ab md bleibt sie beim Scrollen unter dem Menü stehen. --}}
            <div class="z-30 mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-lg border border-gray-200 bg-white p-2.5 shadow-xs md:sticky md:top-[4.75rem] dark:border-gray-700 dark:bg-gray-800">
                <nav aria-label="Kapitel" class="flex min-w-0 flex-1 gap-1.5 overflow-x-auto [scrollbar-width:none] md:flex-wrap">
                    @foreach ($survey->chapters as $chapter)
                        <a href="#kapitel-{{ $chapter->chapter['number'] }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                            <span class="font-bold text-blue-700 dark:text-blue-400">{{ $chapter->chapter['number'] }}</span>{{ $chapter->chapter['name'] }}
                        </a>
                    @endforeach
                    @if ($isLeader)
                        <a href="#bemerkung" class="inline-flex shrink-0 items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Bemerkung</a>
                    @endif
                </nav>
                <span data-save-status role="status" class="text-sm text-gray-600 data-[tone=error]:text-red-700 data-[tone=ok]:text-green-700 max-md:hidden dark:text-gray-400 dark:data-[tone=error]:text-red-400 dark:data-[tone=ok]:text-green-400">Speichert automatisch</span>
                @unless ($isLeader)
                    <button type="submit" name="action" value="close" class="button-small-primary max-md:hidden!" {{ new \Illuminate\View\ComponentAttributeBag($closeConfirm) }}>Abschliessen …</button>
                @endunless
            </div>

            <x-surveys.bewertungs-schluessel :answers="$answers" class="mb-1"/>
            <p class="mb-4 text-xs text-gray-600 dark:text-gray-400">
                Antippen von «Kernkompetenz» oder einer Bewertung zeigt die Beschreibung.
                @if ($isLeader)
                    <span class="inline-flex items-center gap-1">
                        <svg class="size-3" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.5" y="6" width="9" height="6.5" rx="1.2"/><path d="M4.5 6V4.2a2.5 2.5 0 0 1 5 0V6"/></svg>
                        Bewertungen, Kommentare und Bemerkung der Leitenden sehen die Teilnehmenden nicht.
                    </span>
                @endif
            </p>

            @foreach ($survey->chapters as $chapter)
                <details open id="kapitel-{{ $chapter->chapter['number'] }}" class="group/kapitel mb-4 scroll-mt-36 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <summary class="flex cursor-pointer list-none items-center gap-3 bg-gray-50 px-4 py-3 md:px-5 dark:bg-gray-700/50 [&::-webkit-details-marker]:hidden">
                        <h2 class="m-0 text-base font-bold text-gray-900 md:text-lg dark:text-white">{{ $chapter->chapter['number'] }}. {{ $chapter->chapter['name'] }}</h2>
                        <span class="text-sm text-gray-500 max-sm:hidden dark:text-gray-400">{{ $chapter->questions->count() }} {{ $chapter->questions->count() === 1 ? 'Kompetenz' : 'Kompetenzen' }}</span>
                        <svg class="ms-auto size-5 shrink-0 text-gray-500 transition-transform group-open/kapitel:rotate-180" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 8l5 5 5-5"/></svg>
                    </summary>
                    <div class="divide-y divide-gray-100 border-t border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                        @foreach ($chapter->questions as $question)
                            @php
                                $info = $question->competence_text();
                                $number = $question->question['number'];
                                $current = $question[$answerField];
                            @endphp
                            <div class="grid gap-x-5 gap-y-3 px-4 py-4 md:grid-cols-[3rem_minmax(0,1fr)_16.5rem] md:px-5">
                                <span class="hidden h-fit justify-center rounded-md bg-gray-100 py-0.5 text-sm font-bold text-gray-700 md:inline-flex dark:bg-gray-700 dark:text-gray-200">{{ $number }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                        <span class="rounded-md bg-gray-100 px-1.5 py-0.5 text-sm font-bold text-gray-700 md:hidden dark:bg-gray-700 dark:text-gray-200">{{ $number }}</span>
                                        <h3 class="m-0 text-[15px] font-semibold text-gray-900 dark:text-white">{{ $question->question['competence'] }}</h3>
                                        @if ($info)
                                            <x-surveys.core-badge :text="$info"/>
                                        @endif
                                    </div>
                                    <p class="mt-1 mb-0 text-sm text-gray-600 dark:text-gray-400">{{ $question->question['name'] }}</p>
                                </div>
                                <div class="min-w-0 md:row-span-2">
                                    <fieldset class="min-w-0">
                                        <legend class="sr-only">Bewertung {{ $number }} {{ $question->question['competence'] }}</legend>
                                        <div class="grid grid-cols-4 gap-1.5">
                                            @foreach ($answers as $answer)
                                                @php
                                                    $radioId = $question->question['id'].'.'.$round.'.'.$number.$answer['id'];
                                                @endphp
                                                {{-- Antippen wählt die Antwort und zeigt ihre Beschreibung (tooltips.js). Ohne Antwort ist «0» angewählt. --}}
                                                <div class="relative" data-tooltip="{{ $answer['description'] }}">
                                                    <x-forms.radio name="answers[{{ $question['id'] }}]" :value="$answer['id']" :id="$radioId" class="peer sr-only"
                                                        :checked="$current === $answer['id'] || ($current === null && $answer['name'] === '0')"/>
                                                    <label for="{{ $radioId }}" class="flex h-11 cursor-pointer items-center justify-center rounded-md border border-gray-300 bg-white text-base font-bold text-gray-900 select-none hover:bg-gray-50 peer-checked:border-blue-700 peer-checked:bg-blue-700 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-blue-300 md:h-9 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-600">{{ $answer['name'] }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                    @if ($isLeader && ($question->answer_first || $question->answer_second))
                                        <p class="mt-1.5 mb-0 flex items-center justify-end gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                            TN selbst:
                                            @if ($question->answer_first)
                                                <span class="text-gray-500 dark:text-gray-400">1.</span><x-surveys.rating-chip :answer="$question->answer_first" :small="true"/>
                                            @endif
                                            @if ($question->answer_second)
                                                <span class="text-gray-500 dark:text-gray-400">2.</span><x-surveys.rating-chip :answer="$question->answer_second" :small="true"/>
                                            @endif
                                        </p>
                                    @elseif ($round === 2 && $question->answer_first)
                                        {{-- 2. Selbsteinschätzung: die eigene erste Einschätzung zum Vergleich --}}
                                        <p class="mt-1.5 mb-0 flex items-center justify-end gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                            1. Selbsteinschätzung:
                                            <x-surveys.rating-chip :answer="$question->answer_first" :small="true"/>
                                        </p>
                                    @endif
                                </div>
                                {{-- Kommentar erst auf Klick; ein zugeklapptes Feld wird trotzdem mitgeschickt --}}
                                <details data-comment class="group/kommentar min-w-0 md:col-start-2" @if (filled($question[$commentField])) open @endif>
                                    <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 text-sm font-medium text-blue-700 hover:underline dark:text-blue-400 [&::-webkit-details-marker]:hidden">
                                        <svg class="size-3.5 group-open/kommentar:hidden" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 2v10M2 7h10"/></svg>
                                        Kommentar
                                    </summary>
                                    <x-forms.text-area name="comments[{{ $question['id'] }}]" :value="$question[$commentField]" rows="2" class="mt-1.5" aria-label="Kommentar zu {{ $number }} {{ $question->question['competence'] }}"/>
                                </details>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endforeach

            @if ($isLeader)
                <section id="bemerkung" class="mb-4 scroll-mt-36 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-4 py-3 md:px-5 dark:border-gray-700">
                        <h2 class="m-0 text-base font-bold text-gray-900 md:text-lg dark:text-white"><label for="comment" class="text-base font-bold text-gray-900 md:text-lg dark:text-white">Bemerkung</label></h2>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Nur für Leitende, erscheint auf Vergleich und Druckansicht</span>
                    </div>
                    <div class="p-4 md:px-5">
                        <x-forms.text-area name="comment" rows="3"/>
                    </div>
                </section>
            @endif

            {{-- Auf dem Handy bleibt die Zeile über dem Menü unten stehen --}}
            <div class="mb-8 flex flex-wrap items-center gap-x-3 gap-y-2 max-md:sticky max-md:bottom-[calc(4rem+env(safe-area-inset-bottom))] max-md:z-30 max-md:rounded-lg max-md:border max-md:border-gray-200 max-md:bg-white max-md:p-2.5 max-md:shadow-md dark:max-md:border-gray-700 dark:max-md:bg-gray-800">
                @unless ($isLeader)
                    <button type="submit" name="action" value="close" class="button-small-primary" {{ new \Illuminate\View\ComponentAttributeBag($closeConfirm) }}>{{ $survey->surveyName() }} abschliessen …</button>
                @endunless
                <button type="submit" name="action" value="save" class="button-small">Speichern</button>
                <span data-save-status class="text-sm text-gray-600 data-[tone=error]:text-red-700 data-[tone=ok]:text-green-700 md:hidden dark:text-gray-400 dark:data-[tone=error]:text-red-400 dark:data-[tone=ok]:text-green-400">Speichert automatisch</span>
                <span class="text-sm text-gray-500 max-md:hidden dark:text-gray-400">Änderungen werden automatisch gespeichert.@unless ($isLeader) Nach dem Abschliessen kannst du nichts mehr ändern.@endunless</span>
            </div>
        </x-forms.form>

        @if ($isLeader)
            <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :card="true"/>
        @endif
        <x-surveys.radar-chart/>
    @endforeach
@endsection

@push('scripts')
    @vite('resources/js/pages/tooltips.js')
    @include('home.radar')
    @vite('resources/js/pages/survey-autosave.js')
@endpush
