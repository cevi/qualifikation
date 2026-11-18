@aware(['model'])

@if($label != '')<label for="{{ $id }}" class="field-label">{{ $label }}</label>@endif
<textarea name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'field-input']) }} {{ $required ? 'required' : ''}}>{{ $current($model) }}</textarea>
