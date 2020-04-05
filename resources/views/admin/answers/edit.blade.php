@extends('layouts.admin')

@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/answers">Antworten</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Antworten</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($answer, ['method' => 'Patch', 'action'=>['AdminAnswersController@update',$answer->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Antwort:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('count', 'Wertung:') !!}
                            {!! Form::text('count', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Antwort', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($answer, ['method' => 'DELETE', 'action'=>['AdminAnswersController@destroy',$answer->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Antwort löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>   
 

@endsection