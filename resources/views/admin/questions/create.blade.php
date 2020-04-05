@extends('layouts.admin')
@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/questions">Fragen</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
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
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminQuestionsController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('chapter_id', 'Kapitel:') !!}
                        {!! Form::select('chapter_id', [''=>'Wähle Kapitel'] + $chapters, null,  ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                            {!! Form::label('number', 'Nummer:') !!}
                            {!! Form::text('number', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>


                    <div class="form-group">
                            {!! Form::label('competence', 'Kompetenz:') !!}
                            {!! Form::text('competence', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Frage Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
</div>
@endsection