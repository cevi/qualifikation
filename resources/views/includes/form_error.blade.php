@if (count($errors) > 0)
    <x-layouts.alert type="danger">
        <ul class="list-disc ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-layouts.alert>
@endif
