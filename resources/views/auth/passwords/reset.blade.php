@extends('layouts.app')

@section('content')
    <x-auth.card :title="__('Reset Password')">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <x-auth.field name="email" type="email" :label="__('E-Mail Address')" :value="$email ?? old('email')" required autocomplete="email" autofocus/>
            <x-auth.field name="password" type="password" :label="__('Password')" required autocomplete="new-password"/>
            <x-auth.field name="password_confirmation" id="password-confirm" type="password" :label="__('Confirm Password')" required autocomplete="new-password"/>

            <div class="md:grid md:grid-cols-12 md:gap-x-4">
                <div class="md:col-span-6 md:col-start-5">
                    <button type="submit" class="button-primary">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </div>
        </form>
    </x-auth.card>
@endsection
