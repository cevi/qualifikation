@aware(['model'])
@if(!$withoutLabel && $label != '')
    <label for="{{ $id }}" class="field-label">{{ $label }}</label>
@endif
@php($selected = (string) $current($model))
<select name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'field-input']) }} {{ $required ? 'required' : '' }}>
    @if($placeholder !== null)
        <option value="" @selected($selected === '')>{{ $placeholder }}</option>
    @endif
    @foreach ($collection as $key => $option)
        <option value="{{ $key }}" @selected((string) $key === $selected)>{{ $option }}</option>
    @endforeach
</select>
