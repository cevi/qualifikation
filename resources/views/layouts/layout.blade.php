@extends('layouts.app')

@section('content')
    <div class="container">
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
                            @if($user->iscampleader())
                                @foreach ($surveys as $survey)
                                    <li><a href="{{route('home.compare',$survey['user_id'])}}">{{$survey->name}} ({{$survey->user['username']}})</a></li>
                                @endforeach

                            @else
                                @foreach ($surveys as $survey)
                                    <li><a href="{{route('home.survey',$survey->id)}}">{{$survey->name}} ({{$survey->user['username']}})</a></li>
                                @endforeach
                            @endif
                        </ul>
                    @endif
                    <!-- /.row -->
                </div>
            </div>

        </div>
    </div>
@endsection