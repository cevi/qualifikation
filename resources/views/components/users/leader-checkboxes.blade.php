{{-- Leitende einer Person, mehrere möglich und alle gleichgestellt (Person erstellen/bearbeiten). Sendet leader_ids[]. --}}
@props(['leaders', 'selected' => []])
@php
    $checked = array_map('intval', old('leader_ids', $selected));
@endphp
<fieldset class="mb-2.5">
    <legend class="field-label">Leitende:</legend>
    {{-- Damit "nichts angekreuzt" ankommt; angekreuzte leader_ids[] überschreiben das leere Feld. --}}
    <input type="hidden" name="leader_ids" value="">

    @if (empty($leaders))
        <p class="text-sm text-gray-500 dark:text-gray-400">Im Kurs gibt es noch keine Leitenden.</p>
    @else
        <div class="max-h-60 overflow-y-auto rounded-lg border border-gray-300 bg-gray-50 px-2.5 py-1.5 dark:border-gray-600 dark:bg-gray-700">
            @foreach ($leaders as $id => $name)
                <div class="flex items-center py-1">
                    <input type="checkbox" name="leader_ids[]" value="{{ $id }}" id="leader_{{ $id }}" class="field-check" @checked(in_array($id, $checked, true))>
                    <label for="leader_{{ $id }}" class="select-none ms-2 text-sm font-medium text-gray-900 dark:text-white">{{ $name }}</label>
                </div>
            @endforeach
        </div>
    @endif
</fieldset>
