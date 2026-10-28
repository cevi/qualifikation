@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                <x-forms.form :action="route('admin.camps.store')" accept-charset="UTF-8" method="POST">
                    <x-forms.container> 
                        <x-forms.text label="Name:" name="name" required=true/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.select label="Kurstyp:" name="camp_type_id" :collection="$camptypes" :withEmptyOption="true" required=true/>
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
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @php
                    $table = [
                        'url' => route('camps.table'),
                        'label' => 'Kurse',
                        'columns' => [
                            ['key' => 'name', 'label' => 'Name', 'link' => 'edit_url'],
                            ['key' => 'username', 'label' => 'Kursleiter'],
                            ['key' => 'email', 'label' => 'E-Mail'],
                            ['key' => 'camp_type', 'label' => 'Kurstyp'],
                            ['key' => 'group', 'label' => 'Gruppe'],
                            ['key' => 'end_date', 'label' => 'End-Datum'],
                            ['key' => 'finish', 'label' => 'Abgeschlossen'],
                            ['key' => 'counter', 'label' => '# Qualifikationen'],
                            ['key' => 'actions', 'label' => 'Aktionen', 'type' => 'actions'],
                        ],
                        'defaultSort' => [['id' => 'camp_type', 'desc' => false], ['id' => 'group', 'desc' => false], ['id' => 'name', 'desc' => false]],
                        'mobile' => ['title' => 'name', 'kicker' => 'camp_type', 'lines' => ['username', 'end_date']],
                    ];
                @endphp
                <div data-vue-component="data-table" data-props="{{ json_encode($table) }}">
                    <noscript>Die Liste braucht JavaScript.</noscript>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/islands.js')
@endpush
