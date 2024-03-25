@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($survey, ['method' => 'PATCH', 'action'=>['AdminSurveysController@update', $survey->id]]) !!}

                <div class="form-group">
                    {!! Form::label('survey_status_id', 'Status:') !!}
                    {!! Form::select('survey_status_id', [''=>'Wähle Status'] + $survey_statuses_id, null,  ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('update', 'Allfällige Einträge löschen:') !!}
                    {!! Form::checkbox('update',   '1', null) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Änderungen speichern', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}

                @include('includes.form_error')
            </div>
        </div>
    </div>
@endsection
