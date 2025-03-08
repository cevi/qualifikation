@extends('layouts.layout')

@section('survey_content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    @foreach ($surveys as $survey)
        <x-page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        @if(!$aktUser->isTeilnehmer())
            <p>
                <a type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800"
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

                     <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th rowspan="2" width="50px" class="px-6 py-3">Nr.</th>
                                <th rowspan="2" width="150px" class="px-6 py-3">Kompetenz</th>
                                <th rowspan="2" width="300px" class="px-6 py-3">Thema</th>
                                <th colspan="2" width="250px" class="px-6 py-3">1. Selbsteinschätzung</th>
                                @if($camp['secondsurveyopen'])
                                    <th colspan="2" width="250px" class="px-6 py-3">2. Selbsteinschätzung</th>
                                @endif
                                @if(!$aktUser->isTeilnehmer())
                                    <th colspan="2" width="250px" class="px-6 py-3">Leiter</th>
                                @endif
                            </tr>
                            <tr>
                                <th width="50px" class="px-6 py-3"></th>
                                <th width="200px" class="px-6 py-3">Kommentar</th>
                                @if($camp['secondsurveyopen'])
                                    <th width="50px" class="px-6 py-3"></th>
                                    <th width="200px" class="px-6 py-3">Kommentar</th>
                                @endif
                                @if(!$aktUser->isTeilnehmer())
                                    <th width="50px" class="px-6 py-3"></th>
                                    <th width="200px" class="px-6 py-3">Kommentar</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($chapter->questions as $question)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200 {{$question->competence_text() ? 'core_competence':''}}">
                                <td width="50px" class="px-6 py-4">{{$question->question['number']}}</td>
                                <td width="150px" class="px-6 py-4">{{$question->question['competence']}}</td>
                                <td width="300px" class="px-6 py-4"
                                    @if($question->competence_text())
                                        {{Popper::pop($question->competence_text())}}
                                    @endif
                                >
                                    {{$question->question['name']}}
                                    @if($question->competence_text())
                                        <i class="fas fa-info-circle"></i>
                                    @endif
                                </td>
                                <td width="50px" class="px-6 py-4" {{ Popper::pop($question->answer_first['description'])}}>{{$question->answer_first['name']}}</td>
                                <td width="200px" class="px-6 py-4">{{$question['comment_first']}}</td>

                                @if($camp['secondsurveyopen'])
                                <td width="50px" class="px-6 py-4" {{ Popper::pop($question->answer_second['description'])}}>{{$question->answer_second['name']}}</td>
                                <td width="200px" class="px-6 py-4">{{$question['comment_second']}}</td>
                                @endif
                                @if(!$aktUser->isTeilnehmer())
                                    <td width="50px" class="px-6 py-4" {{ Popper::pop($question->answer_leader['description'])}}>{{$question->answer_leader['name']}}</td>
                                    <td width="200px" class="px-6 py-4">{{$question['comment_leader']}}</td>
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
