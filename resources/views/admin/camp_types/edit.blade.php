@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($camp_type, ['method' => 'Patch', 'action'=>['CampTypesController@update',$camp_type]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Änderungen speichern', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
                @if($camp_type->camps->count()==0)
                    {!! Form::model($camp_type, ['method' => 'DELETE', 'action'=>['CampTypesController@destroy',$camp_type], 'id'=> "DeleteForm"]) !!}
                        <div class="form-group">
                            {!! Form::submit('Kurs löschen', ['class' => 'btn btn-danger confirm'])!!}
                        </div>
                    {!! Form::close()!!}
                @endif
                </div>
        </div>
    </div>
@endsection