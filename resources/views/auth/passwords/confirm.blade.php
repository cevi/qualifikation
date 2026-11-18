@extends('layouts.app')

@section('content')
    <x-auth.card :title="__('Confirm Password')">
        <p class="mb-4">{{ __('Please confirm your password before continuing.') }}</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <x-auth.field name="password" type="password" :label="__('Password')" required autocomplete="current-password"/>

            <div class="md:grid md:grid-cols-12 md:gap-x-4">
                <div class="md:col-span-8 md:col-start-5">
                    <button type="submit" class="button-primary">
                        {{ __('Confirm Password') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="font-medium text-blue-600 hover:underline dark:text-blue-500" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-auth.card>
@endsection
