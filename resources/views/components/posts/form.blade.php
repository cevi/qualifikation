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
            <input name="delete_file" id="delete_file" type="checkbox" value="{{ $post['delete_file'] }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ $post['delete_file'] ? 'checked' : '' }}>
            <label for="delete_file" id="delete-file-label" class="select-none ms-2 text-sm font-medium text-heading">{{ $post->file ? 'Bestehende Datei ' . $post->filename() . ' löschen' : 'Bestehende Datei löschen' }}</label>
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