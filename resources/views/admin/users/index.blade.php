@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        @if (Session::has('deleted_user'))
            <x-layouts.alert type="danger">{{ session('deleted_user') }}</x-layouts.alert>
        @endif
        <div class="flex flex-wrap items-start">
            <a href="{{route('admin.users.create')}}" class="button-primary" role="button">Person manuell erstellen oder zuordnen</a>
            @if (config('app.import_db'))
                <button class="button-purple me-2 disabled:cursor-not-allowed disabled:opacity-50" type="button" id="showImport" data-url="{{ route('users.import') }}"
                        title="{{$has_api_token ? '' : 'Deine Region hat den DB-Import nicht freigeschalten.' }}" {{$has_api_token ? '' : 'disabled'}}>
                    Personen aus Cevi-DB importieren
                </button>
            @endif
            {!! Form::open(['action' => 'AdminUsersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'files' => true, 'class' => 'flex flex-wrap items-start gap-x-2']) !!}
                {!! Form::file('csv_file', ['accept' => '.json', 'class' => 'field-file mb-2 w-auto! max-w-xs', 'aria-label' => 'Teilnehmendenliste (JSON)']) !!}
                {{ Form::submit('Teilnehmendenliste hochladen', ['class' => 'button-purple']) }}
            {!! Form::close() !!}
        </div>
        <br>
        @php
            $table = [
                'url' => route('users.table'),
                'label' => 'Personen',
                'columns' => [
                    ['key' => 'name', 'label' => 'Name', 'link' => 'edit_url', 'linkTitle' => 'Person bearbeiten'],
                    ['key' => 'avatar', 'label' => 'Bild', 'type' => 'image', 'link' => 'profile_url', 'linkTitle' => 'Zum Profil'],
                    ['key' => 'email', 'label' => 'E-Mail'],
                    ['key' => 'role', 'label' => 'Rolle'],
                    ['key' => 'leader', 'label' => 'Leitende'],
                    ['key' => 'classification', 'label' => 'Klassifizierung'],
                    ['key' => 'camp', 'label' => 'Kurs'],
                    ['key' => 'group', 'label' => 'Abteilung'],
                    ['key' => 'password_changed', 'label' => 'Passwortänderung'],
                    ['key' => 'last_login', 'label' => 'Letztes Login'],
                ],
                'defaultSort' => [['id' => 'role', 'desc' => false], ['id' => 'leader', 'desc' => false], ['id' => 'name', 'desc' => false]],
                'sortParam' => 'order',
                'mobile' => ['title' => 'name', 'image' => 'avatar', 'kicker' => 'role', 'lines' => ['leader', 'group']],
            ];
        @endphp
        <div class="my-4" data-vue-component="data-table" data-props="{{ json_encode($table) }}">
            <noscript>Die Liste braucht JavaScript.</noscript>
        </div>
        <a href="{{route('admin.users.create')}}" class="button-primary" role="button">Person manuell erstellen oder zuordnen</a>
    </div>

@endsection
@push('scripts')
    @vite('resources/js/islands.js')
    @vite('resources/js/pages/users-import.js')
@endpush
