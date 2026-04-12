@aware(['model'])
@props([
    'id',
    'name',
    'label' => '',
    'value' => null,
    'required' => false,
])

<input name="{{ $name }}" id="{{ $id }}" type="checkbox" value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{$value ? 'checked' : ''}}>
@if($label != '')<label for="{{ $id }}" class="select-none ms-2 text-sm font-medium text-heading">{!! $label !!}</label>@endif