@extends('layouts.app')

@section('content')
    <div class="container">
        @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p>{{ Session::get('message') }}</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">

            <!-- Blog Post Content Column -->
            <div class="col-lg-8">

             @yield('survey_content')   

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">
                <!-- Blog Categories Well -->
                <div class="well">
                    <h4>Deine Umfragen</h4>
                    @if ($surveys)
                        
                        <ul class="list-unstyled">
                            @foreach ($surveys as $survey)
                                <li>
                                    @if($user->iscampleader() || $survey['survey_status_id'] >= config('status.survey_fertig') || 
                                        ($user['role_id'] === config('status.role_Teilnehmer') && $survey['survey_status_id'] >= config('status.survey_abgeschlossen')))
                                        <a href="{{route('survey.compare',$survey['user_id'])}}">{{$survey->user['username']}}</a>
                                    @else
                                        <a href="{{route('survey.survey', $survey->id)}}">{{$survey->user['username']}}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <!-- /.row -->
                </div>
            </div>

        </div>
    </div>
@endsection