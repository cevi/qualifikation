<x-forms.form :action="$route" accept-charset="UTF-8" method="POST" :model="$post" enctype="multipart/form-data">
    <x-forms.hidden name="post_id" :value="$post->id"/>
    <x-forms.container>
        <x-forms.text-area label="Rückmeldung (// für Vorgaben eingeben):" name="comment" required=true rows="10"/>
    </x-forms.container>
     @if($chooseFromUser)
        <x-forms.container>
            <x-forms.select label="Benutzer:" name="camp_user_id" :collection="$campusers" :with-empty-option="true"/>
        </x-forms.container>
    @endif
    <div id="delete-file-container" class="{{ $post->file ? '' : 'hidden' }}">
        <x-forms.container>
            <x-forms.checkbox name="delete_file" id="delete_file" :label="$post->file ? 'Bestehende Datei ' . e($post->filename()) . ' löschen' : 'Bestehende Datei löschen'" :value="$post['delete_file']"/>
        </x-forms.container>
        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">oder</p>
    </div>
    <x-forms.container>
        <x-forms.file name="file"/>
    </x-forms.container>
    <x-forms.container>
       <x-forms.checkbox label="Sichtbar für Qualifikation" name="show_on_survey" value="{{$post['show_on_survey']}}"/>
    </x-forms.container>
    <x-forms.container>
        <x-forms.button type="submit" class="btn btn-primary">
            {{ $post['id'] ? 'Änderungen Speichern' : 'Rückmeldung Erstellen' }}
        </x-forms.button>
    </x-forms.container> 
</x-forms.form>