@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        @if (Session::has('deleted_user'))
            <x-layouts.alert type="danger">{{ session('deleted_user') }}</x-layouts.alert>
        @endif
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 lg:col-span-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
            @if (config('app.import_db'))
                <div class="min-w-0 col-span-12 lg:col-span-4">
                    <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button" id="showImport" data-url="{{ route('users.import') }}"
                            title="{{$has_api_token ? '' : 'Deine Region hat den DB-Import nicht freigeschalten.' }}" {{$has_api_token ? '' : 'disabled'}}>
                        Personen aus Cevi-DB importieren
                    </button>
                </div>
            @endif
            <div class="min-w-0 col-span-12 lg:col-span-4">
                {!! Form::open(['action' => 'AdminUsersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'files' => true]) !!}
                <div class="mb-2.5">
                    {!! Form::file('csv_file', ['accept' => '.json', 'class' => 'field-file']) !!}
                </div>
                {{ Form::submit('Teilnehmerliste hochladen', ['class' => 'button-primary']) }}
                {!! Form::close() !!}
            </div>

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
                    ['key' => 'leader', 'label' => 'Leiter'],
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
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 lg:col-span-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    @vite('resources/js/islands.js')
    @vite('resources/js/pages/users-import.js')
@endpush
