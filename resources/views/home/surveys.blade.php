@extends('layouts.layout')

@section('survey_content')
        <h1>Hallo {{$aktUser->username}}</h1> 

        @if ($surveys)
            <div class="card table-responsive">           
            <table class="table">
                <thead>
                    <tr>
                        @if ($aktUser->isTeilnehmer() || ($aktUser->isLeader()))
                            <th scope="col">Name</th>
                        @endif
                        @if (!$aktUser->isTeilnehmer())
                            <th scope="col">Vergleich</th>
                        @endif
                        <th scope="col">Teilnehmer</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($surveys)
                        @foreach ($surveys as $survey)
                            @if($survey->MySurvey() || $aktUser->isCampleader())
                                <tr>
                                    @if (($aktUser->isTeilnehmer() && $survey['survey_status_id'] < config('status.survey_tnAbgeschlossen'))|| 
                                        $aktUser->isLeader())
                                        <td>
                                            @if ($survey->TNisAllowed())
                                                <a href="{{route('survey.survey', $survey->slug)}}">{{$survey->SurveyName()}}</a>
                                            @else
                                                {{$survey->SurveyName()}}</td>
                                            @endif
                                        </td>
                                    @endif
                                    @if ($aktUser->isTeilnehmer() && $survey['survey_status_id'] >= config('status.survey_tnAbgeschlossen'))
                                        <td><a href="{{route('survey.compare', $survey->user->slug)}}">{{$survey->SurveyName()}}</a></td>
                                    @endif
                                    @if (!$aktUser->isTeilnehmer())
                                        <td><a href="{{route('survey.compare', $survey->user->slug)}}">Vergleich</a></td>
                                    @endif
                                    <td>
                                        @if (!$aktUser->isTeilnehmer())
                                        <a href="{{route('home.profile', $survey->user->slug)}}">{{$survey->user['username']}}</a>
                                        @else
                                            {{$survey->user['username']}}
                                        @endif
                                    </td>
                                    <td>{{$survey->survey_status['name']}}</td>
                                </tr>    
                            @endif
                        @endforeach

                    @endif

                </tbody>
            </table>
            </div>
            @if ($aktUser->isLeader())       
                <div class="row d-flex align-items-md-stretch"> 
                    @foreach($surveys as $survey) 
                        <div class="col-lg-6 col-md-12" id="Chart-{{$loop->iteration}}">
                            <div class="card">
                                <a href="{{route('survey.compare',$survey->slug)}}">
                                    <div  class="card-header d-flex justify-content-between align-items-center">
                                        <h2 class="h5 display">{{$survey->user['username']}}</h2>
                                        <h2 class="h5 display">{{$survey->survey_status['name']}}</h2>
                                    </div>
                                </a>
                                <div role="tabpanel" class="collapse show">
                                    <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="radarChart-{{$loop->iteration}}"  width="100%" height="100%"></canvas>
                                    </div>
                                    </div>
                                </div>  
                            </div>
                        </div>     
                    @endforeach
                </div>
            @endif
        @endif

        
@endsection

@section('scripts')
    @if ($aktUser->isLeader()) 
        @include('home.radar')
    @endif
@endsection
