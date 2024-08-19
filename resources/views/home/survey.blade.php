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
                        <table class="table">
                            <tbody>
                            <tr class="{{$question->competence_text() ? 'core_competence':''}}">
                                <td width="50px">{{$question->question['number']}}</td>
                                <td width="150px">{{$question->question['competence']}}</td>
                                <td width="300px"
                                @if($question->competence_text())
                                    {{Popper::pop($question->competence_text())}}
                                    @endif>{{$question->question['name']}}

                                    @if($question->competence_text())
                                        <i class="fas fa-info-circle"></i>
                                    @endif
                                </td>
                            </tr>
                            </tbody>

                        </table>
                        <div class="form-group row" style="padding-left:10px; padding-right:10px">
                            <div class="col-sm-12">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        @foreach ($answers as $answer)
                                            @if ($aktUser->isLeader())

                                                <td width="50px" {{ Popper::pop($answer['description'])}}>
                                                    {{ Form::radio('answers['.$question['id'].']', $answer['id'], ($question['answer_leader_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_leader_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.3.' . $question->question['number'].$answer['id']])}}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                </td>
                                            @else
                                                @isFirstSurvey($survey->survey_status_id)
                                                <td width="50px" {{ Popper::pop($answer['description'])}}>
                                                    {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_first_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_first_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.1.' .$question->question['number'].$answer['id']]) }}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                </td>
                                                @else
                                                    <td width="50px" {{ Popper::pop($answer['description'])}}>
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_second_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_second_id']===$answer['id']) ? true : false), ["id" => $question->question['sort-index']-1 . '.2.' . $question->question['number'].$answer['id']]) }}
                                                        {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                    </td>
                                                @endif
                                            @endif
                                        @endforeach
                                    </tr>
                                    </tbody>

                                </table>
                            </div>
                            <div class="col-sm-2">
                                @if($aktUser->isLeader())
                                    {!! Form::label('comment_leader', 'Kommentar:') !!}
                                @else
                                    @isFirstSurvey($survey->survey_status_id)
                                    {!! Form::label('comment_first', 'Kommentar:') !!}
                                    @else
                                        {!! Form::label('comment_second', 'Kommentar:') !!}
                                    @endif
                                @endif
                            </div>
                            <div class="col-sm-10">
                                @if($aktUser->isLeader())
                                    {!! Form::textarea('comments['.$question['id'].']', $question['comment_leader'], ['class' => 'form-control', 'rows'=> 2]) !!}
                                @else
                                    @isFirstSurvey($survey->survey_status_id)
                                    {!! Form::textarea('comments['.$question['id'].']', $question['comment_first'], ['class' => 'form-control', 'rows'=> 2]) !!}
                                    @else
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_second'], ['class' => 'form-control', 'rows'=> 2]) !!}
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        @if($aktUser->isLeader())
            <br>
            {!! Form::label('comment', 'Bemerkung:') !!}
            {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows'=> 2]) !!}
            <br>
        @endif
        <div class="form-group row">
            <div class="col-sm-4">
                
                <button type="submit" name="action" value="save" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    {{$survey->surveyName()}} speichern
                </button>
            </div>
            @if ($aktUser->isTeilnehmer())
                <div class="col-sm-4">
                    <button type="submit" name="action" value="close"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
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
