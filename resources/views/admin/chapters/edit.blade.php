@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$chapter" :action="action('AdminChaptersController@update', $chapter->id)">
                    <div class="mb-2.5">
                        <x-forms.text name="number" label="Nummer:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Kapitel:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                    </div>
                </x-forms.form>

                <x-forms.form method="DELETE" :model="$chapter" :action="action('AdminChaptersController@destroy', $chapter->id)">
                <div class="mb-2.5">
                    <x-forms.button type="submit" delete>Kapitel löschen</x-forms.button>
                </div>
                </x-forms.form>
                </div>
        </div>
    </div>    
@endsection