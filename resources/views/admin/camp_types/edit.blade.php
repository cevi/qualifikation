@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$camp_type" :action="action('CampTypesController@update', $camp_type)">
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Name:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                    </div>
                </x-forms.form>
                @if($camp_type->camps->count()==0)
                    <x-forms.form method="DELETE" :model="$camp_type" :action="action('CampTypesController@destroy', $camp_type)" id="DeleteForm">
                        <div class="mb-2.5">
                            <x-forms.button type="submit" delete>Kurs-Typ löschen</x-forms.button>
                        </div>
                    </x-forms.form>
                @endif
                </div>
        </div>
    </div>
@endsection