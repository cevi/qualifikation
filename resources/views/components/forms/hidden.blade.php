@aware(['model'])

@if($label != '')<label for="{{ $id }}">{{ $label }}</label>@endif
<input type="hidden" name="{{$name}}" id="{{$id}}" value="{{ $current($model) }}" {{ $attributes }}/>
