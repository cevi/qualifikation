@extends('layouts.layout')

@section('survey_content')
        <h1>Hallo {{$user->username}}</h1> 

        @if ($surveys)
                      
            <table class="table">
                <thead>
                    <tr>
                        @if ($user['role_id'] === config('status.role_Teilnehmer') || ($user->isLeader()))
                            <th scope="col">Name</th>
                        @endif
                        @if ($user->isLeader() || $user->isCampleader())
                            <th scope="col">Vergleich</th>
                        @endif
                        <th scope="col">Teilnehmer</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($surveys)
                        @foreach ($surveys as $survey)
                        <tr>
                            @if (($user['role_id'] === config('status.role_Teilnehmer') && $survey['survey_status_id'] < config('status.survey_abgeschlossen')) || ($user->isLeader()))
                                <td><a href="{{route('survey.survey', $survey->id)}}">{{$survey['name']}}</a></td></td>
                            @endif
                            @if ($user['role_id'] === config('status.role_Teilnehmer') && $survey['survey_status_id'] >= config('status.survey_abgeschlossen'))
                                <td><a href="{{route('survey.compare', $survey->user['id'])}}">{{$survey['name']}}</a></td></td>
                            @endif
                            @if ($user->isLeader() || $user->isCampleader())
                                <td><a href="{{route('survey.compare', $survey->user['id'])}}">Vergleich</a></td></td>
                            @endif
                            <td>{{$survey->user['username']}}</td>
                            <td>{{$survey->survey_status['name']}}</td> 
                            @if($survey['survey_status_id']===config('status.survey_offen'))
                                <td><a href="{{route('survey.finish', $survey->id)}}" type="button" class="btn btn-success btn-sm">Abschliessen</a></td>
                            @elseif($survey['survey_status_id']===config('status.survey_abgeschlossen') && $user->isleader())
                                <td><a href="{{route('survey.finish', $survey->id)}}" type="button" class="btn btn-success btn-sm">Abschliessen</a></td>
                            @else
                            <td></td>
                            @endif
                        </tr>    
                        @endforeach

                    @endif

                </tbody>
            </table>
        @endif

        
@endsection
