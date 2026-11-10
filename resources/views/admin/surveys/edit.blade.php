@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$survey" :action="action('AdminSurveysController@update', $survey->id)">

                <div class="mb-2.5">
                    <x-forms.select name="survey_status_id" label="Status:" :collection="$survey_statuses_id" placeholder="Wähle Status" :value="$survey->survey_status_id?->value"/>
                </div>

                <div class="mb-2.5">
                    <x-forms.checkbox name="update" label="Allfällige Einträge löschen"/>
                </div>

                <div class="mb-2.5">
                    <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                </div>
                </x-forms.form>

                @include('includes.form_error')

                
                <x-forms.form method="DELETE" :action="action('AdminSurveysController@destroy', $survey)">
                <div class="mb-2.5">
                    <x-forms.button type="submit" delete>Lösche Qualifikation</x-forms.button>
                </div>
                </x-forms.form>
            </div>
        </div>
    </div>
@endsection
