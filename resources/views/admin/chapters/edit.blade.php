@extends('layouts.admin')

@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/chapters">Kapitel</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Kapitel</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($chapter, ['method' => 'Patch', 'action'=>['AdminChaptersController@update',$chapter->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('number', 'Nummer:') !!}
                            {!! Form::text('number', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Kapitel:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Kapitel', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($chapter, ['method' => 'DELETE', 'action'=>['AdminChaptersController@destroy',$chapter->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Kapitel löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>   
 

@endsection