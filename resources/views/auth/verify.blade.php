@extends('layouts.app')

@section('content')
    <x-auth.card :title="__('Verify Your Email Address')">
        @if (session('resent'))
            <x-layouts.alert>{{ __('A fresh verification link has been sent to your email address.') }}</x-layouts.alert>
        @endif

        {{ __('Before proceeding, please check your email for a verification link.') }}
        {{ __('If you did not receive the email') }},
        <form class="inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="font-medium text-blue-600 hover:underline dark:text-blue-500">{{ __('click here to request another') }}</button>.
        </form>
    </x-auth.card>
@endsection
