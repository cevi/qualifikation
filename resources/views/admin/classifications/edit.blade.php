@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($classification, ['method' => 'Patch', 'action'=>['AdminClassificationController@update',$classification->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Antwort:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Update Klassifizierung', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>    
@endsection