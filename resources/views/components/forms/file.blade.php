@aware(['model'])

@if($label != '')<label class="field-label" for="{{ $name }}">{{ $label }} </label>@endif
<input type="file" id="{{ $id }}" name="{{ $name }}" class="field-file" {{ $required ? 'required' : ''}} {{$attributes}}>