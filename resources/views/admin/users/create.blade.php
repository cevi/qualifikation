@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <h3 class="text-3xl font-bold">Person Suchen:</h3>
                <br>
                {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@add']) !!}
                <div class="mb-2.5">
                        {!! Form::label('username', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('username', null, ['class' => 'field-input', 'placeholder' => 'name@abt', 'required', 'list' => 'user-suggestions', 'autocomplete' => 'off', 'data-search-url' => route('searchajaxuser')]) !!}
                        <datalist id="user-suggestions"></datalist>
                </div>
                <div class="mb-2.5">
                    {!! Form::label('role_id', 'Rolle:', ['class' => 'field-label']) !!}
                    {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'field-input', 'required']) !!}
                </div>
                {!! Form::hidden('user_id') !!}
                <div class="mb-2.5">
                    {!! Form::submit('Person hinzufügen', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <h3 class="text-3xl font-bold">Person Erstellen:</h3>
                <br>
                @include('includes.form_error')
                {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@store',  'files' => true]) !!}
                <div class="mb-2.5">
                        {!! Form::label('username', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('username', null, ['class' => 'field-input', 'placeholder' => 'name@abt', 'required']) !!}
                </div>
                <div id="user_information_form">
                    <div class="mb-2.5">
                            {!! Form::label('email', 'E-Mail:', ['class' => 'field-label']) !!}
                            {!! Form::text('email', null, ['class' => 'field-input', 'placeholder' => 'name@abt.ch', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('avatar', 'Bild:', ['class' => 'field-label']) !!}
                        {!! Form::file('avatar', ['class' => 'photo field-file']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::hidden('cropped_photo_id', null, ['class' => 'field-input', 'id' => 'cropped_photo_id']) !!}
                    </div>

                    <div class="mb-2.5">
                        {!! Form::label('role_id', 'Rolle:', ['class' => 'field-label']) !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                        {!! Form::label('group_id', 'Abteilung:', ['class' => 'field-label']) !!}
                        {!! Form::select('group_id', [''=>'Wähle Abteilung'] + $groups, null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <x-users.leader-checkboxes :leaders="$leaders"/>

                    <div class="mb-2.5">
                        {!! Form::label('password', 'Passwort:', ['class' => 'field-label']) !!}
                        {!! Form::password('password', ['class' => 'field-input', 'required']) !!}
                    </div>
                </div>

                <div class="mb-2.5">
                    {!! Form::submit('Person erstellen', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}

            </div>
        </div>
    </div>
    <x-users.photo-cropper/>
@endsection
@push('scripts')
    @vite('resources/js/pages/user-search.js')
@endpush
