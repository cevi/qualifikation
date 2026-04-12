@aware(['model'])

@if($label != '')<label for="{{ $name }}">{{ $label }}</label>@endif
<input type="hidden" name="{{$name}}" id="{{$id}}" value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes->merge(['class' => 'form-control']) }}/>