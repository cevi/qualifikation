{{-- Gemeinsame Felder der Dialoge «Neuer Kurs» und «Kurs bearbeiten»; $prefix macht die IDs eindeutig. --}}
<x-forms.container>
    <x-forms.text label="Name:" name="name" :id="$prefix.'name'" required=true/>
</x-forms.container>
@if ($withLeader ?? false)
    {{-- Optionen füllt camps-index.js: nur Personen mit Rolle Kursleitung in diesem Kurs --}}
    <x-forms.container>
        <x-forms.select label="Kursleitung:" name="user_id" :id="$prefix.'user_id'" aria-describedby="{{ $prefix }}user_id_hint"/>
        <p id="{{ $prefix }}user_id_hint" class="mt-1 text-sm text-gray-500 dark:text-gray-400" data-leader-hint>
            Zur Auswahl stehen die Personen mit der Rolle Kursleitung in diesem Kurs.
        </p>
    </x-forms.container>
@endif
<x-forms.container>
    <x-forms.select label="Kurstyp:" name="camp_type_id" :id="$prefix.'camp_type_id'" :collection="$camptypes" :withEmptyOption="true" required=true/>
</x-forms.container>
<x-forms.container>
    <x-forms.checkbox label="Quali-Ablauf kontrollieren" name="status_control" :id="$prefix.'status_control'"/>
</x-forms.container>
<x-forms.container>
    <x-forms.text label="Schlussdatum:" name="end_date" :id="$prefix.'end_date'" required=true type="date"/>
</x-forms.container>
<x-forms.container>
    <x-forms.select label="Organisierende Gruppe:" name="group_id" :id="$prefix.'group_id'" :collection="$groups" :withEmptyOption="true" required=true/>
</x-forms.container>
@if (config('app.import_db'))
    <x-forms.container>
        <x-forms.text label="Kurs ID (Cevi-DB)" name="foreign_id" :id="$prefix.'foreign_id'"/>
    </x-forms.container>
@endif
