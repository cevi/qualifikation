@extends('layouts.app')

@section('content')
    <x-auth.card :title="__('Register')">
        <div class="md:grid md:grid-cols-12 md:gap-x-4">
            <x-auth.hitobito-buttons class="md:col-span-6 md:col-start-5"/>
        </div>
        <div class="hr-label">oder</div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <x-honeypot />

            <x-auth.field name="username" :label="__('Benutzer')" required autocomplete="name" autofocus/>
            <x-auth.field name="email" type="email" :label="__('E-Mail Addresse')" required autocomplete="email"/>
            <x-auth.field name="password" type="password" :label="__('Passwort')" required autocomplete="new-password"/>
            <x-auth.field name="password_confirmation" id="password-confirm" type="password" :label="__('Passwort bestätigen')" required autocomplete="new-password"/>

            <div class="md:grid md:grid-cols-12 md:gap-x-4">
                <div class="md:col-span-6 md:col-start-5">
                    <button type="submit" class="button-primary w-full">
                        {{ __('Registrieren') }}
                    </button>
                </div>
            </div>
        </form>
    </x-auth.card>
@endsection
