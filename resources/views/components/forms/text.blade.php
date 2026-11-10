@aware(['model'])

@if($label != '')<label for="{{ $id }}" class="field-label">{{ $label }}</label>@endif
{{-- Passwörter nie vorbelegen (wie Form::password) --}}
<input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $id }}"
    @if($type === 'checkbox')
        value="{{ $current($model) }}" {{ $attributes }} class="field-check" {{$value ? 'checked' : ''}}
    @elseif($type === 'password')
        {{ $attributes->merge(['class' => 'field-input']) }}
    @else
        value="{{ $current($model) }}" {{ $attributes->merge(['class' => 'field-input']) }}
    @endif
    {{ $required ? 'required' : ''}}>
