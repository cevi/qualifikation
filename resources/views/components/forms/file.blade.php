@aware(['model'])

@if($label != '')<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="{{ $name }}">{{ $label }} </label>@endif
<input type="file" id="{{ $id }}" name="{{ $name }}" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" {{ $required ? 'required' : ''}} {{$attributes}}>