@extends('layouts.layout')
@section('survey_content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camps->any())
            <x-layouts.alert type="danger">
            <ul class="list-disc ps-4">
                @foreach ($errors->camps->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-layouts.alert>
        @endif
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                 <x-forms.form :action="route('home.camps.store')" accept-charset="UTF-8" method="POST">
                    <x-forms.container> 
                        <x-forms.text label="Name:" name="name" required=true/>
                    </x-forms.container> 
                    <x-forms.container>                    
                        <div class="flex items-baseline justify-between gap-2">
                            <label for="camp_type_id" class="field-label">Kurstyp:</label>
                            <a tabindex="-1" class="text-xs text-gray-500 hover:underline dark:text-gray-400"
                                href="{{ route('camp_types.create') }}">
                                Eigenen Kurstyp erstellen?
                            </a>
                        </div>
                        <x-forms.select label="Kurstyp:" name="camp_type_id" :collection="$camptypes" :withEmptyOption="true" :withoutLabel="true"  required=true/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.checkbox label="Quali-Ablauf kontrollieren" name="status_control"/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.text label="Schlussdatum:" name="end_date" required=true type="date"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.select label="Organisierende Gruppe:" name="group_id" :collection="$groups" :withEmptyOption="true" required=true/>
                    </x-forms.container>
                    @if (config('app.import_db'))
                        <x-forms.container>
                            <x-forms.text label="Kurs ID (Cevi-DB)" name="foreign_id"/>
                        </x-forms.container>
                    @endif
                    <x-forms.container>
                        <x-forms.button type="submit">
                            Kurs erstellen
                        </x-forms.button>
                    </x-forms.container> 
                </x-forms.form>
            </div>
        </div>
    </div>
@endsection
