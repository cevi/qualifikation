@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($competence, ['method' => 'Patch', 'action'=>['AdminCompetencesController@update',$competence->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Beschreibung:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('question_id', 'Kompetenz:') !!}
                        {!! Form::select('question_id', [''=>'Wähle Kompetenz'] + $questions, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('camp_type_id', 'Kurs-Typ:') !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurs-Typ'] + $camp_types, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Update Kernkompetenz', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}

                {!! Form::model($competence, ['method' => 'DELETE', 'action'=>['AdminCompetencesController@destroy',$competence->id]]) !!}
                <div class="form-group">
                    {!! Form::submit('Kernkompetenz löschen', ['class' => 'btn btn-danger'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>    
@endsection