@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p>{{ Session::get('message') }}</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @yield('survey_content')   
    </div>
@endsection