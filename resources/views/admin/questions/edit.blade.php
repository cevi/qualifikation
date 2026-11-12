@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$question" :action="action('AdminQuestionsController@update', $question->id)">
                    <div class="mb-2.5">
                        <x-forms.select name="chapter_id" label="Kapitel:" :collection="$chapters" placeholder="Wähle Kapitel" required/>
                    </div>

                    <div class="mb-2.5">
                            <x-forms.text name="number" label="Nummer:" required/>
                    </div>

                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Name:" required/>
                    </div>

                    <div class="mb-2.5">
                            <x-forms.text name="competence" label="Kompetenz:" required/>
                    </div>

                    <div class="mb-2.5">
                        <x-forms.text name="sort-index" label="Index zur Anzeige im Radar (Im Uhrzeigensinn ab 12Uhr):" required/>
                    </div>

                    <div class="mb-2.5">
                        <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                    </div>
                </x-forms.form>

                @if (Auth::user()->isAdmin())
                    <x-forms.form method="DELETE" :model="$question" :action="action('AdminQuestionsController@destroy', $question->id)">
                    <div class="mb-2.5">
                        <x-forms.button type="submit" delete>Kompetenz löschen</x-forms.button>
                    </div>
                    </x-forms.form>
                @endif
                </div>
        </div>
    </div>
@endsection
