@extends('layouts.layout')

@section('survey_content')
    @foreach ($surveys as $survey)
        <x-layouts.page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        <p>
            Die <span class='core_competence'>blau hinterlegten Kompetenzen</span> sind die Kernkompetenzen für diese
            Ausbildungsstufe.
        </p>
        <x-surveys.bewertungs-schluessel :answers="$answers"/>
        @php
            // Welche Einschätzung die Person ausfüllt: Leitende ihre, Teilnehmende die 1. oder 2. Selbsteinschätzung.
            // Die Runde steckt in der ID der Radio-Knöpfe (Frage.Runde.NummerAntwort).
            [$round, $answerField, $commentField] = match (true) {
                $aktUser->isLeader() => [3, 'answer_leader_id', 'comment_leader'],
                ($survey->survey_status_id ?? \App\Enums\SurveyStatus::Neu)->isFirstRound() => [1, 'answer_first_id', 'comment_first'],
                default => [2, 'answer_second_id', 'comment_second'],
            };
        @endphp
        <x-forms.form method="PATCH" :model="$survey" :action="action('SurveysController@update', $survey->slug)" class="survey-form">
        <div data-accordion="collapse" id="accordion-flush">
            @foreach ($survey->chapters as $ch_key => $chapter)

                <x-surveys.chapter-title :chapter="$chapter"/>
                <div id="accordion-flush-body-{{$chapter->chapter['number']}}" class="hidden"
                     aria-labelledby="accordion-flush-heading-{{$chapter->chapter['number']}}">
                    @foreach ($chapter->questions as $q_key => $question)
                        <div class="mx-2 my-4 grid grid-cols-12 gap-x-2 sm:gap-x-8 text-xl {{$question->competence_text() ? 'core_competence':''}}">
                            <div class="min-w-0 sm:col-span-1 col-span-2">{{$question->question['number']}}</div>
                            <div class="min-w-0 sm:col-span-3 col-span-10">{{$question->question['competence']}}</div>
                            <div class="min-w-0 sm:col-span-8 col-span-12" data-tooltip="{{ $question->competence_text() }}">{{$question->question['name']}}

                                @if($question->competence_text())
                                    <i class="fas fa-info-circle"></i>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2.5" style="padding-left:10px; padding-right:10px">
                            <div>
                                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($answers as $answer)
                                        <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                            <div class="flex items-center ps-3" data-tooltip="{{ $answer['description'] }}">
                                                {{-- Ohne Antwort ist «0» angewählt --}}
                                                @php
                                                    $radioId = $question->question['id'].'.'.$round.'.'.$question->question['number'].$answer['id'];
                                                @endphp
                                                <x-forms.radio name="answers[{{ $question['id'] }}]" :value="$answer['id']" :id="$radioId"
                                                    :checked="$question[$answerField] === $answer['id'] || ($question[$answerField] === null && $answer['name'] === '0')"/>
                                                <label for="{{ $radioId }}" class="w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300">{{ $answer['name'] ? $answer['name'] : ' 0 ' }}</label>
                                        </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mt-4 mb-6">
                                <x-forms.text-area name="comments[{{ $question['id'] }}]" label="Kommentar:" :value="$question[$commentField]" rows="2"/>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
        @if($aktUser->isLeader())
            <br>
            <x-forms.text-area name="comment" label="Bemerkung:" rows="2"/>
            <br>
        @endif
        <div class="mb-2.5 flex flex-wrap items-start">
            <button type="submit" name="action" value="save" class="button-primary">
                {{$survey->surveyName()}} speichern
            </button>
            @if ($aktUser->isTeilnehmer())
                <button type="submit" name="action" value="close" class="button-secondary"
                        data-confirm="{{$survey->surveyName()}} abschliessen?"
                        data-confirm-text="Danach kannst du deine Einschätzung nicht mehr ändern.">
                    {{$survey->surveyName()}} speichern und abschliessen
                </button>
            @endif
        </div>
        </x-forms.form>
        @if($aktUser->isLeader())
            <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'"/>
        @endif
        <x-surveys.radar-chart/>

    @endforeach

@endsection

@push('scripts')
    @vite('resources/js/pages/tooltips.js')
    @include('home.radar')
    @vite('resources/js/pages/survey-autosave.js')
@endpush
