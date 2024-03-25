@extends('layouts.layout')

@section('survey_content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    @foreach ($surveys as $survey)
        <x-page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        @if(!$aktUser->isTeilnehmer())
            <p>
                <a type="button" class="btn btn-primary btn-sm"
                   href="{{route('survey.downloadPDF', $survey)}}">Druckansicht</a>
            </p>
            <p>
                Die Bewertungen und die Kommentare der Leiter sind für die Teilnehmer nicht ersichtlich.
            </p>
        @endif
        <p>
            Die <span class='core_competence'>blau hinterlegten Kompetenzen</span> sind die Kernkompetenzen für deine
            Ausbildungsstufe.
        </p>
        <x-bewertungs-schluessel :answers="$answers"/>
        <div data-accordion="collapse" id="accordion-flush">
            @foreach ($survey->chapters as $chapter)

                <x-chapter-title :chapter="$chapter"/>
                <div id="accordion-flush-body-{{$chapter->chapter['number']}}" class="hidden"
                     aria-labelledby="accordion-flush-heading-{{$chapter->chapter['number']}}">

                    <table class="table col-sm-12">
                        <thead>
                        <tr>
                            <th rowspan="2" width="50px">Nr.</th>
                            <th rowspan="2" width="150px">Kompetenz</th>
                            <th rowspan="2" width="300px">Thema</th>
                            <th colspan="2" width="250">1. Selbsteinschätzung</th>
                            @if($camp['secondsurveyopen'])
                                <th colspan="2" width="250">2. Selbsteinschätzung</th>
                            @endif
                            @if(!$aktUser->isTeilnehmer())
                                <th colspan="2" width="250">Leiter</th>
                            @endif
                        </tr>
                        <tr>
                            <th width="50px"></th>
                            <th width="200px">Kommentar</th>
                            @if($camp['secondsurveyopen'])
                                <th width="50px"></th>
                                <th width="200px">Kommentar</th>
                            @endif
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

                                @if($camp['secondsurveyopen'])
                                <td width="50px" {{ Popper::pop($question->answer_second['description'])}}>{{$question->answer_second['name']}}</td>
                                <td width="200px">{{$question['comment_second']}}</td>
                                @endif
                                @if(!$aktUser->isTeilnehmer())
                                    <td width="50px" {{ Popper::pop($question->answer_leader['description'])}}>{{$question->answer_leader['name']}}</td>
                                    <td width="200px">{{$question['comment_leader']}}</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
        @if (!$aktUser->isTeilnehmer())
            <br>
            <div>
                <h3>Bemerkung:</h3>
                {!! nl2br($survey['comment']) !!}
            </div>
            <br>
        @endif
        @if ($aktUser->id == $survey->campUser->leader_id)
            <div class="form-group row">
                {!! Form::model($survey, ['method' => 'Patch', 'action'=>['SurveysController@finish',$survey->id]]) !!}
                {!! Form::submit('Qualifikationsprozess abschliessen', ['class' => 'btn btn-primary'])!!}
            </div>
            <br>
        @endif
        {!! Form::close()!!}
        @if($aktUser->isLeader() || $aktUser->isCampleader())
            <x-post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :editable="false"/>
        @endif
        <x-radar-chart/>
    @endforeach

@endsection

@push('scripts')
    @include('popper::assets')
    @include('home.radar')
@endpush
