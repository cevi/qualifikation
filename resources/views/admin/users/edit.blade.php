@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                @include('includes.form_error')
                <x-forms.form method="PATCH" :model="$user" :action="action('AdminUsersController@update', $user->id)" enctype="multipart/form-data">
                <div class="mb-2.5">
                    <x-forms.file name="avatar" label="Bild:" class="photo"/>
                </div>
                <div class="mb-2.5">
                    <x-forms.hidden name="cropped_photo_id" class="field-input"/>
                </div>
                <div class="mb-2.5">
                        <x-forms.text name="username" label="Name:" placeholder="name@abt"/>
                </div>
                <div class="mb-2.5">
                        <x-forms.text name="email" label="E-Mail:" placeholder="name@abt.ch"/>
                </div>

                <div class="mb-2.5">
                    <x-forms.select name="role_id" label="Rolle:" :collection="$roles" placeholder="Wähle Rolle" :value="$user->role_id?->value"/>
                </div>

                <div class="mb-2.5">
                    <x-forms.select name="group_id" label="Abteilung:" :collection="$groups" placeholder="Wähle Abteilung" required/>
                </div>

                <x-users.leader-checkboxes :leaders="$leaders" :selected="$selectedLeaders"/>

                <div class="mb-2.5">
                    <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                </div>
                </x-forms.form>
                <x-forms.form method="DELETE" :model="$user" :action="action('AdminUsersController@destroy', $user)" id="DeleteForm">
                <div class="mb-2.5">
                    <x-forms.button type="submit" data-confirm="Person löschen?" data-confirm-text="Sicher, dass die Person gelöscht werden soll?" delete class="confirm">Person löschen</x-forms.button>
                </div>
                </x-forms.form>

            </div>
        </div>
    </div>
    <x-users.photo-cropper/>
@endsection
