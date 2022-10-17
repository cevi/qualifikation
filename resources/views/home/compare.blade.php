@extends('layouts.layout')

@section('survey_content')
<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    @foreach ($surveys as $survey)
        <h1>Übersicht</h1>

        <p class="lead">
            von {{$survey->campuser->user['username']}}
        </p>
        @if(!$aktUser->isTeilnehmer())
            <p>
                <a type="button" class="btn btn-primary btn-sm" href="{{route('survey.downloadPDF', $survey)}}">Druckansicht</a>
            </p>
            <p>
                Die Bewertungen und die Kommentare der Leiter sind für die Teilnehmer nicht ersichtlich.
            </p>
        @endif
        <p>
            Die <span class = 'core_competence'>blau hinterlegten Kompetenzen</span>  sind die Kernkompetenzen für deine Ausbildungsstufe.
        </p>
        <p>
            <h4>Bewertungsschlüssel</h4>
            <table class="table">
                <thead>
                    <tr>
                        @foreach ($answers as $answer)
                        <th>
                            {{$answer['name']}}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($answers as $answer)
                        <td>
                            {{$answer['description']}}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </p>
        @foreach ($survey->chapters as $chapter)
            <div id="recent-activities-wrapper-{{$chapter->chapter['number']}}" class="card updates activities">
                <a data-toggle="collapse" data-parent="#recent-activities-wrapper-{{$chapter->chapter['number']}}" href="#activities-box-{{$chapter->chapter['number']}}" aria-expanded="true" aria-controls="activities-box">
                    <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 display">
                            {{$chapter->chapter['number']}}. {{$chapter->chapter['name']}}
                        </h2>
                        <i class="fa fa-angle-down"></i>
                    </div>
                </a>
                <div id="activities-box-{{$chapter->chapter['number']}}" role="tabpanel" class="collapse table-responsive">

                    <table class="table col-sm-12">
                        <thead>
                            <tr>
                                <th rowspan="2" width="50px">Nr.</th>
                                <th rowspan="2" width="150px">Kompetenz</th>
                                <th rowspan="2" width="300px">Thema</th>
                                <th colspan="2" width="250">1. Selbsteinschätzung</th>
                                <th colspan="2" width="250">2. Selbsteinschätzung</th>
                                @if(!$aktUser->isTeilnehmer())
                                    <th colspan="2" width="250">Leiter</th>
                                @endif
                            </tr>
                            <tr>
                                <th width="50px"></th>
                                <th width="200px">Kommentar</th>
                                <th width="50px"></th>
                                <th width="200px">Kommentar</th>
                                @if(!$aktUser->isTeilnehmer())
                                    <th width="50px"></th>
                                    <th width="200px">Kommentar</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chapter->questions as $question)
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
                                    <td width="50px" {{ Popper::pop($question->answer_first['description'])}}>{{$question->answer_first['name']}}</td>
                                    <td width="200px">{{$question['comment_first']}}</td>
                                    <td width="50px" {{ Popper::pop($question->answer_second['description'])}}>{{$question->answer_second['name']}}</td>
                                    <td width="200px">{{$question['comment_second']}}</td>
                                    @if(!$aktUser->isTeilnehmer())
                                        <td width="50px" {{ Popper::pop($question->answer_leader['description'])}}>{{$question->answer_leader['name']}}</td>
                                        <td width="200px">{{$question['comment_leader']}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        <div class="card radar-chart-example">
            <div class="card-header d-flex align-items-center">
                <h4>Kompetenzendarstellung</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                <canvas id="radarChart-1"  width="100%" height="100%"></canvas>
                </div>
            </div>
        </div>
        @if ($aktUser->isLeader())
            <div class="form-group row">
                {!! Form::model($survey, ['method' => 'Patch', 'action'=>['SurveysController@finish',$survey->id]]) !!}
                    {!! Form::submit('Qualifikationsprozess abschliessen', ['class' => 'btn btn-primary'])!!}
                {!! Form::close()!!}
            </div>
        @endif
    @endforeach


@endsection

@section('scripts')
    @include('popper::assets')
    @include('home.radar')
@endsection
