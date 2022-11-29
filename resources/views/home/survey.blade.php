@extends('layouts.layout')

@section('survey_content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    @foreach ($surveys as $survey)
        <h1>{{$survey->surveyName()}}</h1>

        <p class="lead">
            von {{$survey->campuser->user['username']}}
        </p>
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
                                                    {{ Form::radio('answers['.$question['id'].']', $answer['id'], ($question['answer_leader_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_leader_id']===$answer['id']) ? true : false), ["id" => (($ch_key*3)+$q_key) . '.3.' . $question->question['number'].$answer['id']])}}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                </td>
                                            @else
                                                @isFirstSurvey($survey->survey_status_id)
                                                <td width="50px" {{ Popper::pop($answer['description'])}}>
                                                    {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_first_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_first_id']===$answer['id']) ? true : false), ["id" => (($ch_key*3)+$q_key) . '.1.' .$question->question['number'].$answer['id']]) }}
                                                    {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                </td>
                                                @else
                                                    <td width="50px" {{ Popper::pop($answer['description'])}}>
                                                        {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_second_id']===NULL) && ($answer['name']==='0') ? true : (($question['answer_second_id']===$answer['id']) ? true : false), ["id" => (($ch_key*3)+$q_key) . '.2.' . $question->question['number'].$answer['id']]) }}
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
        <div class="form-group row">
            <div class="col-sm-4">
                <button type="submit" name="action" value="save" class="btn btn-primary">{{$survey->surveyName()}}
                    speichern
                </button>
            </div>
            @if ($aktUser->isTeilnehmer())
                <div class="col-sm-4">
                    <button type="submit" name="action" value="close"
                            class="btn btn-secondary">{{$survey->surveyName()}} speichern und abschliessen
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

@section('scripts')
    @include('popper::assets')
    @include('home.radar')
@endsection
