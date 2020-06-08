@extends('layouts.layout')

@section('survey_content')
        <h1>Hallo {{$user->username}}</h1> 
        {!! Form::model($user, ['method' => 'PATCH', 'class' => 'card', 'action'=>['UsersController@update', $user->id]]) !!}
        
        <div class="card-header">
            <h3 class="card-title">Mein Profil</h3>
          </div>
            <div class="card-body">
                <div class="form-group">
                    {!! Form::label('username', 'Name:') !!}
                    {!! Form::text('username', null, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('leader', 'Leiter:') !!}
                    {!! Form::text('leader', $user->leader['username'], ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', 'Passwort:') !!}
                    {{-- {!! Form::password('password', null, ['class' => 'form-control']) !!} --}}
                    <input name="password" id="password" type="password" value="password" class="form-control">
                </div>
            </div>
            <div class="card-footer text-right">
                {!! Form::submit('Speichern', ['class' => 'btn btn-primary'])!!}
            </div>
            {!! Form::close()!!}
@endsection