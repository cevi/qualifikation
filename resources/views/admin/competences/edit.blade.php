@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$competence" :action="action('AdminCompetencesController@update', $competence->id)">
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Beschreibung:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.select name="question_id" label="Kompetenz:" :collection="$questions" placeholder="Wähle Kompetenz" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.select name="camp_type_id" label="Kurs-Typ:" :collection="$camp_types" placeholder="Wähle Kurs-Typ" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                    </div>
                </x-forms.form>

                <x-forms.form method="DELETE" :model="$competence" :action="action('AdminCompetencesController@destroy', $competence->id)">
                <div class="mb-2.5">
                    <x-forms.button type="submit" delete>Kernkompetenz löschen</x-forms.button>
                </div>
                </x-forms.form>
                </div>
        </div>
    </div>    
@endsection