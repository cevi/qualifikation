@extends('layouts.admin')

@section('content')
        <div class="container-fluid">
            <!-- Page Header-->
            <x-page-title :title="$title" :help="$help"/>
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
                            {!! Form::label('description', 'Beschreibung:') !!}
                            {!! Form::text('description', null, ['class' => 'form-control', 'required']) !!}
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
@endsection