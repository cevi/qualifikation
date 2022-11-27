@extends('layouts.layout')
@section('survey_content')
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Kurs</h1>
            </header>
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
                        {!! Form::label('camp_type_id', 'Kurstyp:') !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    @if (config('app.import_db'))
                        <div class="form-group">
                            {!! Form::label('group_id', 'Organisierende Gruppe:') !!}
                            {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'form-control']) !!}
                        </div>
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
    </section>
@endsection
