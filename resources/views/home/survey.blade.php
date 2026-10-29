@extends('layouts.layout')

@section('survey_content')
    @foreach ($surveys as $survey)
        <x-layouts.page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        <p>
            Die <span class='core_competence'>blau hinterlegten Kompetenzen</span> sind die Kernkompetenzen für diese
            Ausbildungsstufe.
        </p>
        <x-surveys.bewertungs-schluessel :answers="$answers"/>
        {!! Form::model($survey, ['method' => 'Patch', 'action'=>['SurveysController@update',$survey->slug], 'class' => 'survey-form']) !!}
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
                                                @if ($aktUser->isLeader())
                                                    {{ Form::radio('answers['.$question['id'].']', $answer['id'], ($question['answer_leader_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_leader_id']===$answer['id']) ? true : false), ["id" => $question->question['id'] . '.3.' . $question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500'])}}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ", ['class' => 'w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300']) !!}
                                                @else
                                                    @isFirstSurvey($survey->survey_status_id)
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_first_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_first_id']===$answer['id']) ? true : false), ["id" => $question->question['id'] . '.1.' .$question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500']) }}
                                                        {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ", ['class' => 'w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300']) !!}
                                                    @else
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_second_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_second_id']===$answer['id']) ? true : false), ["id" => $question->question['id'] . '.2.' . $question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500']) }}
                                                        {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ", ['class' => 'w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300']) !!}
                                                    @endif
                                                @endif
                                        </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mt-4 mb-6">
                                @if($aktUser->isLeader())
                                    {!! Form::label('comment_leader', 'Kommentar:', ['class' => 'field-label']) !!}
                                    {!! Form::textarea('comments['.$question['id'].']', $question['comment_leader'], ['class' => 'field-input', 'rows'=> 2]) !!}
                                @else
                                    @isFirstSurvey($survey->survey_status_id)
                                        {!! Form::label('comment_first', 'Kommentar:', ['class' => 'field-label']) !!}
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_first'], ['class' => 'field-input', 'rows'=> 2]) !!}
                                    @else
                                        {!! Form::label('comment_second', 'Kommentar:', ['class' => 'field-label']) !!}    
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_second'], ['class' => 'field-input', 'rows'=> 2]) !!}
                                    @endif
                                @endif
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
            {!! Form::label('comment', 'Bemerkung:', ['class' => 'field-label']) !!}
            {!! Form::textarea('comment', null, ['class' => 'field-input', 'rows'=> 2]) !!}
            <br>
        @endif
        <div class="mb-2.5 flex flex-wrap items-start">
            <button type="submit" name="action" value="save" class="button-primary">
                {{$survey->surveyName()}} speichern
            </button>
            @if ($aktUser->isTeilnehmer())
                <button type="submit" name="action" value="close" class="button-secondary"
                        data-confirm="{{$survey->surveyName()}} abschliessen?"
                        data-confirm-text="Danach kannst du deine Einschätzung nicht mehr ändern."
                        data-confirm-icon="question">
                    {{$survey->surveyName()}} speichern und abschliessen
                </button>
            @endif
        </div>
        {!! Form::close()!!}
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
