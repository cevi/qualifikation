@aware(['model'])
@if(!$withoutLabel)
    <label for="{{$name}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$label}}</label>
@endif
<select name="{{$name}}"  id="{{ $id }}" {{$attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) }} {{ $required ? 'required' : ''}}>
    @if($withEmptyOption)
        <option value="">{{ __('Wähle eine Option') }}</option>
    @endif
    @foreach ($collection as $key => $value)
        <option value="{{ $key }}"
        @if ($key == ($model ? data_get($model, $name) : 0))
            selected="selected"
        @endif
        >{{ $value }}</option>
    @endforeach
</select>