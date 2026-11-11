@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <x-layouts.page-title :title="$title" :help="$help"/>
        <p class="mt-2 mb-4 max-w-4xl text-sm text-gray-600 dark:text-gray-400">
            Kreuze an, wer welche Teilnehmenden leitet. Jedes Kreuz wird sofort gespeichert, mehrere Leitende pro Person sind möglich.
        </p>
        @php
            $props = [
                'participants' => $participants,
                'leaders' => $leaders,
                'url' => url('admin/users/leaders'),
            ];
        @endphp
        <div class="my-4" data-vue-component="leader-matrix" data-props="{{ json_encode($props) }}">
            <noscript>Die Zuteilung braucht JavaScript. Einzeln geht es über «Person bearbeiten».</noscript>
        </div>
        <a href="{{ route('admin.users.index') }}" class="button-secondary" role="button">Zurück zu den Personen</a>
    </div>
@endsection
@push('scripts')
    @vite('resources/js/islands.js')
@endpush
