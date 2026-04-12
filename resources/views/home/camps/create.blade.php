@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camps->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->camps->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-3">
                 <x-forms.form :action="route('home.camps.store')" accept-charset="UTF-8" method="POST">
                    <x-forms.container> 
                        <x-forms.text label="Name:" name="name" required=true/>
                    </x-forms.container> 
                    <x-forms.container>                    
                        <div class="row">
                            <div class="col">
                                <label for="camp_type_id" class="col block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kurstyp:</label>
                            </div>
                            <div class="col-auto">
                                <a tabindex="-1" class="form-text small text-muted"
                                    href="{{ route('camp_types.create') }}">
                                    Eigenen Kurstyp erstellen?
                                </a>
                            </div>
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
