@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($group, ['method' => 'Patch', 'action'=>['AdminGroupsController@update',$group->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('foreign_id', 'Gruppen ID (Cevi-DB):') !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('url', 'URL (Cevi-DB):') !!}
                        {!! Form::text('url', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('api_token', 'API Token (Cevi-DB):') !!}
                        {!! Form::password('api_token', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Update Gruppe', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}

                @error('group')
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">{{ $message }}</div>
                @enderror
                {!! Form::model($group, ['method' => 'DELETE', 'action'=>['AdminGroupsController@destroy',$group->id], 'id'=> "DeleteForm"]) !!}
                <div class="form-group">
                    {!! Form::submit('Gruppe löschen', ['class' => 'btn btn-danger confirm', 'data-confirm' => 'Gruppe löschen?', 'data-confirm-text' => 'Willst du die Gruppe wirklich löschen?'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>
@endsection
