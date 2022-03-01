@extends('layouts.app')

@section('content')
  <div class="container-fluid px-3">
    <div class="row" style="min-height: 85vh !important;">
      <div class="col-md-5 col-lg-6 col-xl-4 px-lg-5 d-flex align-items-center">
        <div class="w-100 py-5">
          <div class="text-center"><img src="/img/logo.svg" alt="..."  class="img-fluid mb-4">
          </div>
          <div class="form-group row">
            <div class="col-md-6 offset-md-3">
                <a
                    class="btn btn-primary form-control{{ $errors->has('hitobito') ? ' is-invalid' : '' }}"
                    style="width: 100%"
                    href="{{ route('login.hitobito') }}">
                    Anmelden mit Cevi-DB
                </a>
                @if ($errors->has('hitobito'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('hitobito') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="hr-label">oder</div>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
              <label for="email">E-Mail</label>
              <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder = "name@abt.ch" value="{{ old('email') }}" required autocomplete="email" autofocus>
              @error('email')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>
            <div class="form-group mb-4">
              <div class="row">
                <div class="col">
                  <label for="password">Passwort</label>
                </div>
                @if (Route::has('password.request'))
                  <div class="col-auto">
                    <a tabindex="-1" class="form-text small text-muted" href="{{ route('password.request') }}">
                        Passwort vergessen?
                      </a>
                  </div>
                @endif
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