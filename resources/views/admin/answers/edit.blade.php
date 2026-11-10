@extends('layouts.admin')

@section('content')
        <div class="px-[15px] md:px-12">
            <!-- Page Header-->
            <x-layouts.page-title :title="$title" :help="$help"/>
            <div class="grid grid-cols-12 sm:gap-x-8">
                <div class="min-w-0 col-span-12 sm:col-span-6">
                    <x-forms.form method="PATCH" :model="$answer" :action="action('AdminAnswersController@update', $answer->id)">
                        <div class="mb-2.5">
                            <x-forms.text name="name" label="Antwort:" required/>
                        </div>
                        <div class="mb-2.5">
                            <x-forms.text name="count" label="Wertung:" required/>
                        </div>
                        <div class="mb-2.5">
                            <x-forms.text name="description" label="Beschreibung:" required/>
                        </div>
                        <div class="mb-2.5">
                            <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                        </div>
                    </x-forms.form>

                    <x-forms.form method="DELETE" :model="$answer" :action="action('AdminAnswersController@destroy', $answer->id)">
                    <div class="mb-2.5">
                        <x-forms.button type="submit" delete>Antwort löschen</x-forms.button>
                    </div>
                    </x-forms.form>
                 </div>
            </div>
        </div>    
@endsection