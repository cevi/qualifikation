@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
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
@endsection