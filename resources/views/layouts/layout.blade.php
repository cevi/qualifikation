@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(Session::has('message'))
            <div id="alert-message" class="alert alert-success alert-dismissible fade show" role="alert">
                <p>{{ Session::get('message') }}</p>
                <button type="button" class="close" data-dismiss-target="#alert-message" aria-label="Schliessen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @yield('survey_content')   
    </div>
@endsection