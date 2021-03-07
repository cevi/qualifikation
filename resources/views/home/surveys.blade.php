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
                            @if (($user['role_id'] === config('status.role_Teilnehmer') && $survey['survey_status_id'] < config('status.survey_abgeschlossen')) || (($user->isLeader())))
                                @if ($survey['survey_status_id'] < config('status.survey_fertig'))
                                    <td><a href="{{route('survey.survey', $survey->id)}}">{{$survey['name']}}</a></td>
                                @else
                                    <td>{{$survey['name']}}</td>
                                @endif
                            @endif
                            @if ($user['role_id'] === config('status.role_Teilnehmer') && $survey['survey_status_id'] >= config('status.survey_abgeschlossen'))
                                <td><a href="{{route('survey.compare', $survey->user['id'])}}">{{$survey['name']}}</a></td>
                            @endif
                            @if ($user->isLeader() || $user->isCampleader())
                                <td><a href="{{route('survey.compare', $survey->user['id'])}}">Vergleich</a></td>
                            @endif
                            <td>
                            @if ($user->isLeader() || $user->isCampleader())
                                <a href="{{route('home.profile', $survey->user['id'])}}">{{$survey->user['username']}}</a>
                            @else
                                {{$survey->user['username']}}</td>
                            @endif
                            <td>{{$survey->survey_status['name']}}</td> 
                            <td>
                                @if(($survey['survey_status_id']===config('status.survey_offen')) || ($survey['survey_status_id']===config('status.survey_abgeschlossen') && $user->isleader()))
                                    <a href="{{route('survey.finish', $survey->id)}}" type="button" class="btn btn-success btn-sm">Abschliessen</a>                       
                                @endif
                            </td>
                        </tr>    
                        @endforeach

                    @endif

                </tbody>
            </table>
        @endif

        
@endsection
