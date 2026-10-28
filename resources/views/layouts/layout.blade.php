@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(Session::has('message'))
            <x-layouts.alert id="alert-message">{{ Session::get('message') }}</x-layouts.alert>
        @endif
        @yield('survey_content')   
    </div>
@endsection