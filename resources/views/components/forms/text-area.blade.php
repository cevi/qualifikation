@aware(['model'])

<label for="{{ $name }}" class="field-label">{{ $label }}</label>
<textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'field-input']) }} {{ $required ? 'required' : ''}}>{{$value ?? ($model ? data_get($model, $name) : '')}}</textarea>