@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help">
            <x-slot:actions>
                <button type="button" id="camp-create-btn" class="button-small-primary">
                    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 2v10M2 7h10"/></svg>
                    Neuer Kurs
                </button>
            </x-slot:actions>
        </x-layouts.page-title>
        @php
            $table = [
                'url' => route('camps.table'),
                'label' => 'Kurse',
                'columns' => [
                    // dialog: Klick öffnet «Kurs bearbeiten» (camps-index.js), Ctrl-Klick die Seite mit offenem Dialog
                    ['key' => 'name', 'label' => 'Name', 'link' => 'edit_url', 'dialog' => true],
                    ['key' => 'username', 'label' => 'Kursleitung'],
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
                'filter' => [
                    'key' => 'finish',
                    'label' => 'Kurse anzeigen',
                    'options' => [
                        ['label' => 'Alle', 'value' => null],
                        ['label' => 'Offene', 'value' => 'Nein'],
                        ['label' => 'Abgeschlossene', 'value' => 'Ja'],
                    ],
                ],
            ];
        @endphp
        <div class="mt-4" data-vue-component="data-table" data-props="{{ json_encode($table) }}">
            <noscript>Die Liste braucht JavaScript.</noscript>
        </div>
    </div>

    {{-- Neuer Kurs --}}
    <dialog id="camp-create-dialog" aria-labelledby="camp-create-title" class="camp-dialog m-auto w-[calc(100%-2rem)] max-w-lg rounded-lg border-0 bg-white p-0 text-gray-900 shadow-xl backdrop:bg-gray-900/60 dark:bg-gray-800 dark:text-white">
        <div class="flex items-center justify-between border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
            <h2 id="camp-create-title" class="text-xl font-semibold">Neuer Kurs</h2>
            <button type="button" data-dialog-close aria-label="Schliessen" class="inline-flex size-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="size-3" aria-hidden="true" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
        <div class="p-4 md:p-5">
            <x-forms.form :action="route('admin.camps.store')" accept-charset="UTF-8" method="POST" id="CampCreateForm">
                @include('admin.camps.fields', ['prefix' => 'create_'])
                <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-forms.button variant="secondary" data-dialog-close>Abbrechen</x-forms.button>
                    <x-forms.button type="submit">Kurs erstellen</x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </dialog>

    {{-- Kurs bearbeiten; Werte und Adressen setzt camps-index.js (data-camp beim Laden, sonst per fetch der edit-Adresse) --}}
    <dialog id="camp-edit-dialog" aria-labelledby="camp-edit-title" data-index-url="{{ route('admin.camps.index') }}"
            @if ($editCamp) data-camp="{{ json_encode($editCamp) }}" @endif
            class="camp-dialog m-auto w-[calc(100%-2rem)] max-w-lg rounded-lg border-0 bg-white p-0 text-gray-900 shadow-xl backdrop:bg-gray-900/60 dark:bg-gray-800 dark:text-white">
        <div class="flex items-center justify-between border-b border-gray-200 p-4 md:p-5 dark:border-gray-600">
            <h2 id="camp-edit-title" class="text-xl font-semibold">Kurs bearbeiten</h2>
            <button type="button" data-dialog-close aria-label="Schliessen" class="inline-flex size-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="size-3" aria-hidden="true" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
        <div class="p-4 md:p-5">
            @if ($errors->any())
                <div role="alert" class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-700 dark:text-red-400">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <x-forms.form action="" accept-charset="UTF-8" method="PATCH" id="CampEditForm">
                @include('admin.camps.fields', ['prefix' => 'edit_', 'withLeader' => true])
                <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-forms.button variant="secondary" data-dialog-close>Abbrechen</x-forms.button>
                    <x-forms.button type="submit">Änderungen speichern</x-forms.button>
                </div>
            </x-forms.form>

            <div class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-600" data-finish-section>
                <h3 class="mb-1 text-base font-semibold">Kurs abschliessen</h3>
                <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                    Löscht alle Qualifikationen, Rückmeldungen und hochgeladenen Dokumente des Kurses. Lade vorher herunter, was du behalten möchtest.
                </p>
                <x-forms.form action="" accept-charset="UTF-8" method="DELETE" id="DeleteForm">
                    <x-forms.button type="submit" class="confirm w-full sm:w-auto" delete=true data-confirm="Kurs abschliessen / löschen?" data-confirm-text="Beim Kurs abschliessen/löschen werden alle Qualifikationen und hochgeladenen Dokumente gelöscht.">
                        Kurs abschliessen / löschen
                    </x-forms.button>
                </x-forms.form>
            </div>
        </div>
    </dialog>
@endsection

@push('scripts')
    @vite(['resources/js/islands.js', 'resources/js/pages/camps-index.js'])
@endpush
