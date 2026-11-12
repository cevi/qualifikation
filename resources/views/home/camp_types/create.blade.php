@extends('layouts.layout')
@section('survey_content')
    <div>
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camp_types->any())
            <x-layouts.alert type="danger">
            <ul class="list-disc ps-4">
                @foreach ($errors->camp_types->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-layouts.alert>
        @endif
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                <x-forms.form :action="action('CampTypesController@store')">
                <div class="mb-2.5">
                    <x-forms.text name="name" label="Name:" required/>
                </div>
                <div class="mb-2.5">
                    <x-forms.button type="submit">Kurs-Typ erstellen</x-forms.button>
                </div>
                </x-forms.form>
            </div>
        </div>
    </div>
@endsection
