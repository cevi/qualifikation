@aware(['model'])

@if($label != '')<label class="field-label" for="{{ $id }}">{{ $label }} </label>@endif
<input type="file" id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'field-file']) }} {{ $required ? 'required' : ''}}>
