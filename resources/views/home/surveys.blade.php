@extends('layouts.layout')

@section('survey_content')
        <h1>Hallo {{$user->username}}</h1> 

        @if ($surveys)
                      
            <table class="table">
                <thead>
                    <tr>
                        @if (!$user->isCampleader())
                        <th scope="col">Name</th>
                        @endif
                        @if ($user->isLeader() || $user->isCampleader())
                            <th scope="col">Vergleich</th>
                        @endif
                        <th scope="col">Teilnehmer</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($surveys)
                        @foreach ($surveys as $survey)
                        <tr>
                            @if (!$user->isCampleader())
                            <td><a href="{{route('home.survey', $survey->id)}}">{{$survey['name']}}</a></td></td>
                            @endif
                            @if ($user->isLeader() || $user->isCampleader())
                                <td><a href="{{route('home.compare', $survey->user['id'])}}">Vergleich</a></td></td>
                            @endif
                            <td>{{$survey->user['username']}}</td>
                            <td>{{$survey->status['name']}}</td>
                        </tr>    
                        @endforeach

                    @endif

                </tbody>
            </table>
        @endif

        
@endsection
