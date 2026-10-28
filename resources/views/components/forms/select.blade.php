@aware(['model'])
@if(!$withoutLabel)
    <label for="{{$name}}" class="field-label">{{$label}}</label>
@endif
<select name="{{$name}}"  id="{{ $id }}" {{$attributes->merge(['class' => 'field-input']) }} {{ $required ? 'required' : ''}}>
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