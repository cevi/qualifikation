@extends('layouts.admin')
@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/surveys">Qualifikationen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Qualifikation von {{$survey->campuser->user['username']}}</h1>
            </header>
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
                        {!! Form::submit('Qualifikation Updaten', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
@endsection
