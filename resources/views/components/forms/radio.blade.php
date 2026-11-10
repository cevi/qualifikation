@aware(['model'])
<input name="{{ $name }}" id="{{ $id }}" type="radio" value="{{ $value }}" {{ $attributes }} @checked($isChecked($model))>
@if($label != '')<label for="{{ $id }}" class="ms-2 text-sm">{{ $label }}</label>@endif
