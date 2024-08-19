@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camp_types->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->camp_types->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'CampTypesController@store']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Kurs-Typ erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection
