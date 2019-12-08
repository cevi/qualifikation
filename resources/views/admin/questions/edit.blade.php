@extends('layouts.admin')

@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/questions">Fragen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Fragen</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($question, ['method' => 'Patch', 'action'=>['AdminQuestionsController@update',$question->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('chapter_id', 'Kapitel:') !!}
                            {!! Form::select('chapter_id', [''=>'Wähle Kapitel'] + $chapters, null,  ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                                {!! Form::label('number', 'Nummer:') !!}
                                {!! Form::text('number', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>


                        <div class="form-group">
                                {!! Form::label('competence', 'Kompetenz:') !!}
                                {!! Form::text('competence', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Frage', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($question, ['method' => 'DELETE', 'action'=>['AdminQuestionsController@destroy',$question->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Frage löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>   
 

@endsection