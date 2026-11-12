@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form method="PATCH" :model="$standard_text" :action="action('StandardTextsController@update', $standard_text)">
                    <div class="mb-2.5">
                        <x-forms.text name="title" label="Title:" required/>
                    </div>
                <div class="mb-2.5">
                    <x-forms.text-area name="content" label="Inhalt:" rows="10" required/>
                </div>
                
                <div class="mb-2.5">
                    <x-forms.button type="submit">Standard-Text aktualisieren</x-forms.button>
                </div>
                </x-forms.form>

                <x-forms.form method="DELETE" :action="action('StandardTextsController@destroy', $standard_text)">
                <div class="mb-2.5">
                    <x-forms.button type="submit" delete>Lösche Standard-Text</x-forms.button>
                </div>
                </x-forms.form>
        </div>
    </div>
@endsection