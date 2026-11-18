@extends('layouts.app')

@section('content')
    <div class="grid min-h-[85vh] grid-cols-12 px-3">
        <div class="col-span-12 flex min-w-0 items-center md:col-span-5 lg:col-span-6 lg:px-12 xl:col-span-4">
            <div class="w-full py-12">
                <x-auth.hitobito-buttons class="mx-auto md:w-1/2"/>
                <div class="hr-label">oder</div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-2.5">
                        <label for="email" class="field-label">E-Mail</label>
                        <input id="email" type="text" class="field-input" name="email" placeholder="name@abt.ch"
                               value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <p class="field-error" role="alert"><strong>{{ $message }}</strong></p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <div class="flex items-baseline justify-between gap-2">
                            <label for="password" class="field-label">Passwort</label>
                            @if (Route::has('password.request'))
                                <a tabindex="-1" class="text-xs text-gray-500 hover:underline dark:text-gray-400"
                                   href="{{ route('password.request') }}">
                                    Passwort vergessen?
                                </a>
                            @endif
                        </div>
                        <input id="password" placeholder="Passwort" type="password" class="field-input" name="password"
                               required autocomplete="current-password">
                        @error('password')
                            <p class="field-error" role="alert"><strong>{{ $message }}</strong></p>
                        @enderror
                    </div>
                    <div class="mb-4 flex items-center">
                        <input class="field-check" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="ms-2 text-sm text-gray-900 dark:text-gray-300" for="remember">
                            Eingeloggt bleiben
                        </label>
                    </div>
                    <!-- Submit-->
                    <button class="button-primary w-full">{{ __('Login') }}</button>
                </form>
            </div>
        </div>
        <div class="col-span-12 hidden md:col-span-7 lg:col-span-6 lg:block xl:col-span-8">
            <!-- Image-->
            <div style="background-image: url(img/login.jpg);" class="-me-3 h-full bg-cover"></div>
        </div>
    </div>
@endsection
