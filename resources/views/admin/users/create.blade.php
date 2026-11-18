@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <h3 class="text-3xl font-bold">Person Suchen:</h3>
                <br>
                <x-forms.form :action="action('AdminUsersController@add')">
                <div class="mb-2.5">
                        {{-- IDs mit Präfix: das Formular «Person erstellen» daneben hat dieselben Feldnamen --}}
                        <x-forms.text name="username" id="search_username" label="Name:" placeholder="name@abt" required list="user-suggestions" autocomplete="off" :data-search-url="route('searchajaxuser')"/>
                        <datalist id="user-suggestions"></datalist>
                </div>
                <div class="mb-2.5">
                    <x-forms.select name="role_id" :collection="$roles" placeholder="Wähle Rolle" id="search_role_id" label="Rolle:" required/>
                </div>
                <x-forms.hidden name="user_id"/>
                <div class="mb-2.5">
                    <x-forms.button type="submit">Person hinzufügen</x-forms.button>
                </div>
                </x-forms.form>
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <h3 class="text-3xl font-bold">Person Erstellen:</h3>
                <br>
                @include('includes.form_error')
                <x-forms.form :action="action('AdminUsersController@store')" enctype="multipart/form-data">
                <div class="mb-2.5">
                        <x-forms.text name="username" label="Name:" placeholder="name@abt" required/>
                </div>
                <div id="user_information_form">
                    <div class="mb-2.5">
                            <x-forms.text name="email" label="E-Mail:" placeholder="name@abt.ch" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.file name="avatar" label="Bild:" class="photo"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.hidden name="cropped_photo_id" class="field-input"/>
                    </div>

                    <div class="mb-2.5">
                        <x-forms.select name="role_id" label="Rolle:" :collection="$roles" placeholder="Wähle Rolle" required/>
                    </div>

                    <div class="mb-2.5">
                        <x-forms.select name="group_id" label="Abteilung:" :collection="$groups" placeholder="Wähle Abteilung" required/>
                    </div>

                    <x-users.leader-checkboxes :leaders="$leaders"/>

                    <div class="mb-2.5">
                        <x-forms.text type="password" name="password" label="Passwort:" required/>
                    </div>
                </div>

                <div class="mb-2.5">
                    <x-forms.button type="submit">Person erstellen</x-forms.button>
                </div>
                </x-forms.form>

            </div>
        </div>
    </div>
    <x-users.photo-cropper/>
@endsection
@push('scripts')
    @vite('resources/js/pages/user-search.js')
@endpush
