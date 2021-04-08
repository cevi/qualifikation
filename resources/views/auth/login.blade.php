@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="row" style="min-height: 85vh !important;">
      <div class="col-md-5 col-lg-6 col-xl-4 px-lg-5 d-flex align-items-center">
        <div class="w-100 py-5">
          <div class="text-center"><img src="img/logo.png" alt="..." style="max-width: 6rem;" class="img-fluid mb-4">
          </div>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
              <label for="username">{{ __('Benutzer') }}</label>
              <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder = "name@abt" value="{{ old('username') }}" required autocomplete="email" autofocus>
              @error('username')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>
            <div class="form-group mb-4">
              <div class="row">
                <div class="col">
                  <label for="password">{{ __('Password') }}</label>
                </div>
              </div>
              <input id="password" placeholder = "Passwort" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror 
            </div>
            <div class="form-group row">
              <div class="col-md-6">
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                      <label class="form-check-label" for="remember">
                          Eingeloggt bleiben
                      </label>
                  </div>
              </div>
            </div>
            <!-- Submit-->
            <button class="btn btn-lg btn-block btn-primary mb-3">{{ __('Login') }}</button>
          </form>
        </div>
      </div>
      <div class="col-12 col-md-7 col-lg-6 col-xl-8 d-none d-lg-block">
        <!-- Image-->
        <div style="background-image: url(img/login.jpg);" class="bg-cover h-100 mr-n3"></div>
      </div>
    </div>
  </div>
  @endsection