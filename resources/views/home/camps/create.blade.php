@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camps->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->camps->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'CampsController@store']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            {!! Form::label('camp_type_id', 'Kurstyp:') !!}
                        </div>
                        <div class="col-auto">
                            <a tabindex="-1" class="form-text small text-muted"
                                href="{{ route('camp_types.create') }}">
                                Eigenen Kurstyp erstellen?
                            </a>
                        </div>
                    </div>
                    {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'form-control ', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('status_control', 'Quali-Ablauf kontrollieren:') !!}
                    {!! Form::checkbox('status_control', 'yes',  false) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('end_date', 'Schlussdatum:') !!}
                    {!! Form::date('end_date', null,  ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('group_id', 'Organisierende Gruppe:') !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'form-control', 'required']) !!}
                </div>
                @if (config('app.import_db'))
                    <div class="form-group">
                        {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):') !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::submit('Kurs erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection
