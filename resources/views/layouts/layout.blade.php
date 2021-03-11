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
            <div class="col-lg-10">

             @yield('survey_content')   

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-2">
                <!-- Blog Categories Well -->
                <div class="well">
                    @if ($users)
                        <h4>Teilnehmer</h4>
                        <ul class="list-unstyled">
                            @foreach ($users as $user_profile)
                                <li>
                                    <a href="{{route('home.profile', $user_profile->id)}}">{{$user_profile->leader_id === Auth::user()->id ? '*' : ''}}{{$user_profile->username}}</a> 
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