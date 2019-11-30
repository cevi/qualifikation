@extends('layouts.admin')

@section('content')

    <h1>Gruppe</h1>
    <div class="col-sm-6">
        {!! Form::model($group, ['method' => 'Patch', 'action'=>['AdminGroupsController@update',$group->id]]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('user_id', 'Gruppenleiter:') !!}
                {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Update Gruppe', ['class' => 'btn btn-primary'])!!}
            </div>
         {!! Form::close()!!}

         {!! Form::model($group, ['method' => 'DELETE', 'action'=>['AdminGroupsController@destroy',$group->id]]) !!}
         <div class="form-group">
             {!! Form::submit('Gruppe löschen', ['class' => 'btn btn-danger'])!!}
         </div>
      {!! Form::close()!!}
    </div>    
 

@endsection