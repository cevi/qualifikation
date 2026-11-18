@aware(['model'])
<input name="{{ $name }}" id="{{ $id }}" type="checkbox" value="{{ $value }}" {{ $attributes->merge(['class' => 'field-check']) }} @checked($isChecked($model)) @required($required)>
@if($label != '')<label for="{{ $id }}" class="select-none ms-2 text-sm font-medium text-heading">{{ $label }}</label>@endif
