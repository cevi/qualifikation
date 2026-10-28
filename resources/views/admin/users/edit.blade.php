@extends('layouts.admin')
@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                @include('includes.form_error')
                {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUsersController@update', $user->id],  'files' => true]) !!}
                <div class="mb-2.5">
                    {!! Form::label('avatar', 'Bild:', ['class' => 'field-label']) !!}
                    {!! Form::file('avatar', ['class' => 'photo field-file']) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::hidden('cropped_photo_id', null, ['class' => 'field-input', 'id' => 'cropped_photo_id']) !!}
                </div>
                <div class="mb-2.5">
                        {!! Form::label('username', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('username', null, ['class' => 'field-input', 'placeholder' => 'name@abt']) !!}
                </div>
                <div class="mb-2.5">
                        {!! Form::label('email', 'E-Mail:', ['class' => 'field-label']) !!}
                        {!! Form::text('email', null, ['class' => 'field-input', 'placeholder' => 'name@abt.ch']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('role_id', 'Rolle:', ['class' => 'field-label']) !!}
                    {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, $user->role_id?->value, ['class' => 'field-input']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('group_id', 'Abteilung:', ['class' => 'field-label']) !!}
                    {!! Form::select('group_id', [''=>'Wähle Abteilung'] + $groups, null, ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('leader_id', 'Gruppenleiter:', ['class' => 'field-label']) !!}
                    {!! Form::select('leader_id', [''=>'Wähle Gruppenleiter'] + $leaders, null,  ['class' => 'field-input']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::submit('Änderungen speichern', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}
                {!! Form::model($user, ['method' => 'DELETE', 'action'=>['AdminUsersController@destroy',$user], 'id'=> "DeleteForm"]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Person löschen', ['data-confirm' => 'Person löschen?', 'data-confirm-text' => 'Sicher, dass die Person gelöscht werden soll?', 'class' => 'confirm button-danger'])!!}
                </div>
                {!! Form::close()!!}

            </div>
        </div>
    </div>
    <x-users.photo-cropper/>
@endsection
