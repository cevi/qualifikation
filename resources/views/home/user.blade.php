@extends('layouts.layout')

@section('survey_content')
    <x-layouts.page-title :title="$title" :help="$help" :header=false/>
    <x-forms.form method="PATCH" :model="$aktUser" :action="action('UsersController@update', $aktUser->id)" class="panel">
        
        <div class="panel-header">
            <h3 class="text-xl font-medium">Mein Profil</h3>
        </div>
        <div class="panel-body">
            @if ($errors->any())
                @include('includes.form_error')
            @endif
            <div class="mb-2.5">
                {{-- Nur zur Anzeige, eigener grauer Stil statt field-input --}}
                <label for="username" class="field-label">Name:</label>
                <input type="text" name="username" id="username" value="{{ $aktUser->username }}" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
            </div>
            <div class="mb-2.5">
                <label for="leader" class="field-label">Leitende:</label>
                <input type="text" name="leader" id="leader" value="{{ $leaderNames }}" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
            </div>
            @if (!$aktUser['demo'])

                <div class="mb-2.5">
                    <x-forms.text name="email" label="E-Mail:"/>
                </div>
                <div class="mb-2.5">
                    <x-forms.text type="password" name="password" label="Passwort:"/>
                </div> 
                <div class="mb-2.5">
                    <x-forms.text type="password" name="password_confirmation" label="Passwort Wiederholen:"/>
                </div> 
            @endif
        </div>
        <div class="panel-footer">
            <x-forms.button type="submit">Speichern</x-forms.button>
        </div>
    </x-forms.form>
@endsection