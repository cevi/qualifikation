@extends('layouts.layout')

@section('survey_content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    @foreach ($surveys as $survey)
        <x-page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        <p>
            Die <span class='core_competence'>blau hinterlegten Kompetenzen</span> sind die Kernkompetenzen für deine
            Ausbildungsstufe.
        </p>
        <x-bewertungs-schluessel :answers="$answers"/>
        {!! Form::model($survey, ['method' => 'Patch', 'action'=>['SurveysController@update',$survey->slug]]) !!}
        <div data-accordion="collapse" id="accordion-flush">
            @foreach ($survey->chapters as $ch_key => $chapter)

                <x-chapter-title :chapter="$chapter"/>
                <div id="accordion-flush-body-{{$chapter->chapter['number']}}" class="hidden"
                     aria-labelledby="accordion-flush-heading-{{$chapter->chapter['number']}}">
                    @foreach ($chapter->questions as $q_key => $question)
                        <div class="mx-2 my-4 row text-xl {{$question->competence_text() ? 'core_competence':''}}">
                            <div class="col-sm-1 col-2">{{$question->question['number']}}</div>
                            <div class="col-sm-3 col-10">{{$question->question['competence']}}</div>
                            <div class="col-sm-8 col-12" 
                            @if($question->competence_text())
                                {{Popper::pop($question->competence_text())}}
                                @endif>{{$question->question['name']}}

                                @if($question->competence_text())
                                    <i class="fas fa-info-circle"></i>
                                @endif
                            </div>
                        </div>
                        <div class="form-group" style="padding-left:10px; padding-right:10px">
                            <div >
                                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($answers as $answer)
                                        <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                            <div class="flex items-center ps-3"  {{ Popper::pop($answer['description'])}}>
                                                @if ($aktUser->isLeader())
                                                    {{ Form::radio('answers['.$question['id'].']', $answer['id'], ($question['answer_leader_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_leader_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.3.' . $question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500'])}}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ", ['class' => 'w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300']) !!}
                                                @else
                                                    @isFirstSurvey($survey->survey_status_id)
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_first_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_first_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.1.' .$question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500']) }}
                                                        {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ", ['class' => 'w-full py-3 ms-2 text-xl font-bold text-gray-900 dark:text-gray-300']) !!}
                                                    @else
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_second_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_second_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.2.' . $question->question['number'].$answer['id']], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500']) }}
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
                                    {!! Form::label('comment_leader', 'Kommentar:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                    {!! Form::textarea('comments['.$question['id'].']', $question['comment_leader'], ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'rows'=> 2]) !!}
                                @else
                                    @isFirstSurvey($survey->survey_status_id)
                                        {!! Form::label('comment_first', 'Kommentar:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_first'], ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'rows'=> 2]) !!}
                                    @else
                                        {!! Form::label('comment_second', 'Kommentar:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}    
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_second'], ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'rows'=> 2]) !!}
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
            {!! Form::label('comment', 'Bemerkung:') !!}
            {!! Form::textarea('comment', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'rows'=> 2]) !!}
            <br>
        @endif
        <div class="form-group row">
            <div class="col-sm-4">
                
                <button type="submit" name="action" value="save" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                    {{$survey->surveyName()}} speichern
                </button>
            </div>
            @if ($aktUser->isTeilnehmer())
                <div class="col-sm-4">
                    <button type="submit" name="action" value="close"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                    {{$survey->surveyName()}} speichern und abschliessen
                    </button>
                </div>
            @endif
        </div>
        {!! Form::close()!!}
        @if($aktUser->isLeader())
            <x-post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'"/>
        @endif
        <x-radar-chart/>

    @endforeach

@endsection

@push('scripts')
    @include('popper::assets')
    @include('home.radar')
@endpush
