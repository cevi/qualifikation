@extends('layouts.admin')

@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/chapters">Kernkompetenz</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Kernkompetenz</h1>
            </header>
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
    </section>   
 

@endsection