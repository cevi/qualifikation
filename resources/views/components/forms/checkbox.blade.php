@aware(['model'])
@props([
    'id',
    'name',
    'label' => '',
    'value' => null,
    'required' => false,
])

<input name="{{ $name }}" id="{{ $id }}" type="checkbox" value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes }} class="field-check" {{$value ? 'checked' : ''}}>
@if($label != '')<label for="{{ $id }}" class="select-none ms-2 text-sm font-medium text-heading">{!! $label !!}</label>@endif