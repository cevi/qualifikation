@extends('layouts.layout')

@section('survey_content')
    <x-layouts.page-title :title="$title" :help="$help" :header=false/>
    {!! Form::model($aktUser, ['method' => 'PATCH', 'class' => 'panel', 'action'=>['UsersController@update', $aktUser->id]]) !!}
        
        <div class="panel-header">
            <h3 class="text-xl font-medium">Mein Profil</h3>
        </div>
        <div class="panel-body">
            @if ($errors->any())
                @include('includes.form_error')
            @endif
            <div class="mb-2.5">
                {!! Form::label('username', 'Name:', ['class' => 'field-label']) !!}
                {!! Form::text('username', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'readonly' => true]) !!}
            </div>
            <div class="mb-2.5">
                {!! Form::label('leader', 'Leitende:', ['class' => 'field-label']) !!}
                {!! Form::text('leader', $leaderNames, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'readonly' => true]) !!}
            </div>
            @if (!$aktUser['demo'])

                <div class="mb-2.5">
                    {!! Form::label('email', 'E-Mail:', ['class' => 'field-label']) !!}
                    {!! Form::text('email', null, ['class' => 'field-input']) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::label('password', 'Passwort:', ['class' => 'field-label']) !!}
                    {!! Form::password('password', ['class' => 'field-input']) !!}
                </div> 
                <div class="mb-2.5">
                    {!! Form::label('password_confirmation', 'Passwort Wiederholen:', ['class' => 'field-label']) !!}
                    {!! Form::password('password_confirmation', ['class' => 'field-input']) !!}
                </div> 
            @endif
        </div>
        <div class="panel-footer">
            {!! Form::submit('Speichern', ['class' => 'button-primary'])!!}
        </div>
    {!! Form::close()!!}
@endsection