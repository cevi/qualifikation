@extends('layouts.app')

@section('content')
    <div class="px-[15px] md:px-12">
        @if(Session::has('message'))
            <x-layouts.alert id="alert-message">{{ Session::get('message') }}</x-layouts.alert>
        @endif
        @yield('survey_content')   
    </div>
@endsection