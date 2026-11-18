{{-- Anmelden über Cevi-DB oder JEMK-DB (Login und Registrieren) --}}
<div {{ $attributes }}>
    <a class="button-purple block w-full" href="{{ route('login.hitobito') }}">Anmelden mit <span class="whitespace-nowrap">Cevi-DB</span></a>
    <a class="button-purple block w-full" href="{{ route('login.hitobito_jemk') }}">Anmelden mit <span class="whitespace-nowrap">JEMK-DB</span></a>
    @error('hitobito')
        <p class="field-error" role="alert"><strong>{{ $message }}</strong></p>
    @enderror
</div>
