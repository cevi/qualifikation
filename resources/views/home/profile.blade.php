@extends('layouts.layout')

{{--
    Profil einer TN-Person für Leitende. Kopf in einer Karte (Foto zum Vergrössern, Name, Leitende, Status mit Links,
    Ampel, Vor/Zurück), darunter Radar und Rückmeldungen. Ab lg nebeneinander und gleich hoch: die Rückmeldungen
    scrollen in ihrer Karte, die Seite bleibt auf einem Bildschirm. Darunter zwei Reiter (ohne Skript, Radio-Knöpfe).
--}}
@section('survey_content')
    @php
        $survey = $surveys->first();
        $group = $user->group['shortname'] ?? '';
        $hasRadar = $surveys->isNotEmpty();
        // Feste Klassen, damit Tailwind sie findet
        $ampel = [
            'red' => ['label' => 'Rot', 'dot' => 'bg-red-500', 'ring' => 'ring-red-500'],
            'yellow' => ['label' => 'Gelb', 'dot' => 'bg-yellow-400', 'ring' => 'ring-yellow-400'],
            'green' => ['label' => 'Grün', 'dot' => 'bg-green-500', 'ring' => 'ring-green-500'],
        ];
    @endphp

    <header class="mt-4 mb-4 flex flex-wrap items-center gap-x-5 gap-y-3 rounded-lg border border-gray-200 bg-white p-3 md:p-4 dark:border-gray-700 dark:bg-gray-800">
        <button type="button" data-photo-open="profil-foto" class="shrink-0 rounded-full focus:ring-4 focus:ring-blue-300 focus:outline-hidden dark:focus:ring-blue-800" title="Foto vergrössern">
            <img src="{{ $user->getAvatar() }}" alt="Foto von {{ $user->username }} vergrössern" class="size-14 rounded-full object-cover md:size-[4.5rem]">
        </button>

        <div class="min-w-0 flex-1 basis-56">
            <p class="mb-0.5 flex flex-wrap items-center gap-x-2 text-xs font-semibold tracking-wide text-gray-600 uppercase dark:text-gray-400">
                Profil
                <span class="inline-flex items-center gap-1 font-medium tracking-normal normal-case">
                    <svg class="size-3" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.5" y="6" width="9" height="6.5" rx="1.2"/><path d="M4.5 6V4.2a2.5 2.5 0 0 1 5 0V6"/></svg>
                    für Teilnehmende nicht sichtbar
                </span>
            </p>
            <h1 class="text-xl leading-tight font-bold md:text-2xl dark:text-white">
                {{ $user->username }}
                @if ($group !== '')
                    <span class="text-sm font-medium text-gray-500 md:text-base dark:text-gray-400">{{ $group }}</span>
                @endif
                @if (isset($help['content']))
                    <x-layouts.help-button/>
                @endif
            </h1>
            <div class="mt-1 flex flex-wrap items-center gap-x-3.5 gap-y-1 text-sm text-gray-700 dark:text-gray-300">
                <span><span class="text-gray-500 dark:text-gray-400">Leitende:</span> {{ $camp_user->leaderNames() ?: '–' }}</span>
                @if ($survey)
                    <x-surveys.status-badge :survey="$survey"/>
                    @if ($survey->TNIsAllowed())
                        <a href="{{ route('survey.survey', $survey->slug) }}" class="font-medium text-blue-700 hover:underline dark:text-blue-400">Quali</a>
                    @endif
                    @if ($survey->SurveyIsAllowed())
                        <a href="{{ route('survey.compare', $survey->slug) }}" class="font-medium text-blue-700 hover:underline dark:text-blue-400">Vergleich</a>
                    @endif
                @endif
            </div>
        </div>

        <fieldset class="flex items-center gap-1 rounded-full border border-gray-200 px-2.5 py-0.5 sm:gap-2 dark:border-gray-700">
            <legend class="sr-only">Ampel</legend>
            <span class="me-1 text-sm font-semibold text-gray-700 max-sm:hidden dark:text-gray-300" aria-hidden="true">Ampel</span>
            @foreach (\App\Enums\Classification::cases() as $classification)
                @php
                    $color = $ampel[$classification->cssClass()];
                    $active = $camp_user->classification_id === $classification;
                @endphp
                <button type="button" class="ampel-btn inline-flex size-11 items-center justify-center rounded-full focus:ring-2 focus:ring-blue-300 focus:outline-hidden sm:size-9 dark:focus:ring-blue-800"
                        data-color="{{ $classification->value }}"
                        data-remote="{{ route('users.changeClassifications', ['id' => $user->id, 'color' => $classification->value]) }}"
                        aria-pressed="{{ $active ? 'true' : 'false' }}" aria-label="{{ $color['label'] }}">
                    <span @class([
                        'size-7 rounded-full sm:size-6',
                        $color['dot'],
                        'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-gray-800 '.$color['ring'] => $active,
                    ])></span>
                </button>
            @endforeach
        </fieldset>

        @if ($previousUser && $nextUser)
            <nav aria-label="Andere Teilnehmende" class="flex gap-2 max-sm:w-full">
                <a href="{{ route('home.profile', $previousUser->slug) }}" class="button-small max-sm:flex-1 max-sm:justify-center">
                    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M11 7H3M6 4 3 7l3 3"/></svg>
                    <span class="truncate">{{ $previousUser->username }}</span>
                </a>
                <a href="{{ route('home.profile', $nextUser->slug) }}" class="button-small max-sm:flex-1 max-sm:justify-center">
                    <span class="truncate">{{ $nextUser->username }}</span>
                    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7h8M8 4l3 3-3 3"/></svg>
                </a>
            </nav>
        @endif
    </header>
    @if (isset($help['content']))
        <x-layouts.drawer-content :help="$help"/>
    @endif

    {{-- Foto gross: Klick daneben oder auf «Schliessen» schliesst (photo-dialog.js, Esc kann der Browser) --}}
    <dialog id="profil-foto" class="m-auto max-w-[min(92vw,40rem)] rounded-lg bg-white p-0 shadow-xl backdrop:bg-gray-900/70 dark:bg-gray-800" aria-label="Foto von {{ $user->username }}">
        <form method="dialog" class="relative">
            <img src="{{ $user->getAvatar() }}" alt="Foto von {{ $user->username }}" class="max-h-[80vh] w-auto rounded-lg">
            <button type="submit" class="absolute top-2 right-2 inline-flex size-9 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow hover:bg-white focus:ring-2 focus:ring-blue-300 focus:outline-hidden" aria-label="Schliessen">
                <svg class="size-4" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 3l8 8M11 3l-8 8"/></svg>
            </button>
        </form>
    </dialog>

    <div class="group/profil">
        @if ($hasRadar)
            <fieldset class="mb-3 grid grid-cols-2 gap-1 rounded-lg bg-gray-200 p-1 lg:hidden dark:bg-gray-700">
                <legend class="sr-only">Ansicht</legend>
                <label class="flex min-h-10 cursor-pointer items-center justify-center gap-1.5 rounded-md text-sm font-medium text-gray-700 has-checked:bg-white has-checked:font-semibold has-checked:text-gray-900 has-checked:shadow-xs has-focus-visible:ring-2 has-focus-visible:ring-blue-300 dark:text-gray-300 dark:has-checked:bg-gray-800 dark:has-checked:text-white">
                    <input type="radio" name="profil-ansicht" id="ansicht-rueckmeldungen" class="sr-only" checked>
                    Rückmeldungen <span class="text-gray-500 dark:text-gray-400">{{ count($posts) }}</span>
                </label>
                <label class="flex min-h-10 cursor-pointer items-center justify-center rounded-md text-sm font-medium text-gray-700 has-checked:bg-white has-checked:font-semibold has-checked:text-gray-900 has-checked:shadow-xs has-focus-visible:ring-2 has-focus-visible:ring-blue-300 dark:text-gray-300 dark:has-checked:bg-gray-800 dark:has-checked:text-white">
                    <input type="radio" name="profil-ansicht" id="ansicht-radar" class="sr-only">
                    Radar
                </label>
            </fieldset>
        @endif

        <div @class(['lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)] lg:gap-4' => $hasRadar])>
            @if ($hasRadar)
                <div class="min-w-0 max-lg:group-has-[#ansicht-rueckmeldungen:checked]/profil:hidden">
                    @foreach ($surveys as $survey)
                        <x-surveys.radar-chart/>
                    @endforeach
                </div>
            @endif
            {{-- Ab lg so hoch wie das Radar (ohne dessen Abstand unten), die Liste scrollt in der Karte --}}
            <div class="relative min-w-0 max-lg:group-has-[#ansicht-radar:checked]/profil:hidden">
                <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :user="$user" :card="true" :clamp="true" :fill="$hasRadar"
                              @class(['lg:absolute lg:inset-x-0 lg:top-0 lg:bottom-8 lg:mb-0' => $hasRadar])>
                    <x-slot:actions>
                        <button type="button" id="new-feedback-btn" class="button-small-primary">
                            <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 2v10M2 7h10"/></svg>
                            Neue Rückmeldung
                        </button>
                    </x-slot:actions>
                </x-posts.post>
            </div>
        </div>
    </div>

    @include('home.feedback_modal', ['route' => route('profile.post.store', $user), 'questions' => $questions])
@endsection


@push('scripts')
    @include('home.radar')
    @include('home.standard-texts-modal')
    @vite('resources/js/pages/profile-ampel.js')
    @vite('resources/js/pages/photo-dialog.js')
@endpush
