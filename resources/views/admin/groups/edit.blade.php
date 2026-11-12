@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$group" :action="action('AdminGroupsController@update', $group->id)">
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Name:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="foreign_id" label="Gruppen ID (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="url" label="URL (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text type="password" name="api_token" label="API Token (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                    </div>
                </x-forms.form>

                @error('group')
                    <x-layouts.alert type="danger">{{ $message }}</x-layouts.alert>
                @enderror
                <x-forms.form method="DELETE" :model="$group" :action="action('AdminGroupsController@destroy', $group->id)" id="DeleteForm">
                <div class="mb-2.5">
                    <x-forms.button type="submit" data-confirm="Gruppe löschen?" data-confirm-text="Willst du die Gruppe wirklich löschen?" delete class="confirm">Gruppe löschen</x-forms.button>
                </div>
                </x-forms.form>
                </div>
        </div>
    </div>
@endsection
