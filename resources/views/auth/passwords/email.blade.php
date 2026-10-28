@extends('layouts.app')

@section('content')
    <x-auth.card :title="__('Passwort zurücksetzen')">
        @if (session('status'))
            <x-layouts.alert>{{ session('status') }}</x-layouts.alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <x-auth.field name="email" type="test" :label="__('E-Mail')" required autocomplete="email" autofocus/>

            <div class="md:grid md:grid-cols-12 md:gap-x-4">
                <div class="md:col-span-6 md:col-start-5">
                    <button type="submit" class="button-primary">
                        {{ __('Password Zurücksetzen Link senden') }}
                    </button>
                </div>
            </div>
        </form>
    </x-auth.card>
@endsection
