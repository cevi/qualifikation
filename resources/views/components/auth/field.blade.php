{{-- Feld der Konto-Seiten: ab md Label links, Feld rechts; Fehlermeldung darunter. Passwörter ohne alten Wert. --}}
@props(['name', 'label', 'type' => 'text', 'id' => null, 'value' => null])
@php($id ??= $name)
<div class="mb-4 md:grid md:grid-cols-12 md:items-center md:gap-x-4">
    <label for="{{ $id }}" class="field-label md:col-span-4 md:mb-0 md:text-right">{{ $label }}</label>
    <div class="md:col-span-6">
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
               @if($type !== 'password') value="{{ $value ?? old($name) }}" @endif
               {{ $attributes->merge(['class' => 'field-input']) }}>
        @error($name)
            <p class="field-error" role="alert"><strong>{{ $message }}</strong></p>
        @enderror
    </div>
</div>
