<button {{ $attributes->merge(['type' => 'button', 'class' => $delete ? 'button-danger' : 'button-primary'])}}>
    {{ $slot }}
</button>