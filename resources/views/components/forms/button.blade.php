<button {{ $attributes->merge(['type' => 'button', 'class' => 'button-'.$variant]) }}>
    {{ $slot }}
</button>
