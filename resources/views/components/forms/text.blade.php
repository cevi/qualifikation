@aware(['model'])

@if($label != '')<label for="{{ $id }}" class="field-label">{{ $label }}</label>@endif
<input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $id }}"
    @if($type === 'checkbox')
        value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes }} class="field-check" {{$value ? 'checked' : ''}} 
    @else
        value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes->merge(['class' => 'field-input']) }} 
    @endif
    {{ $required ? 'required' : ''}}>