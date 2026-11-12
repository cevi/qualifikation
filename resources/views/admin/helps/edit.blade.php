@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help_article"/>
        <div class="grid grid-cols-12 sm:gap-x-8">

            <div class="min-w-0 col-span-12 sm:col-span-6">

                <x-forms.form method="PATCH" :model="$help" :action="action('AdminHelpController@update', $help)">
                <div class="mb-2.5">
                    <x-forms.text name="title" label="Titel:"/>
                </div>
                <div class="mb-2.5">
                    <x-forms.text-area name="content" label="Inhalt:" required rows="10"/>
                </div>

                <div class="mb-2.5">
                    <x-forms.button type="submit">Hilfe-Artikel aktualisieren</x-forms.button>
                </div>
                </x-forms.form>

                <x-forms.form method="DELETE" :action="action('AdminHelpController@destroy', $help)">
                <div class="mb-2.5">
                    <x-forms.button type="submit" delete>Lösche Hilfe-Artikel</x-forms.button>
                </div>
                </x-forms.form>
            </div>
        </div>
        <div class="grid grid-cols-12 sm:gap-x-8">
            @include('includes.form_error')
        </div>
    </div>
@endsection
