@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                <x-forms.form :action="route('admin.camps.update', $camp)" accept-charset="UTF-8" method="Patch" :model="$camp">
                    <x-forms.container> 
                        <x-forms.text label="Name:" name="name" required=true/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.select label="Kursleitung:" name="user_id" :collection="$users"/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.select label="Kurstyp:" name="camp_type_id" :collection="$camptypes" :withEmptyOption="true" required=true/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.checkbox label="Quali-Ablauf kontrollieren" name="status_control" :checked="$camp['status_control']"/>
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
                            Änderungen speichern
                        </x-forms.button>
                    </x-forms.container> 
                </x-forms.form>
                <x-forms.form :action="route('admin.camps.destroy', $camp)" accept-charset="UTF-8" method="DELETE" id="DeleteForm" :model="$camp">
                    <x-forms.container>
                        <x-forms.button type="submit" class="confirm" delete=true data-confirm="Kurs abschliessen / löschen?" data-confirm-text="Beim Kurs abschliessen/löschen werden alle Qualifikationen und hochgeladenen Dokumente gelöscht.">
                            Kurs abschliessen / löschen
                        </x-forms.button>
                    </x-forms.container> 
                </x-forms.form>

                </div>
        </div>
    </div>
@endsection
