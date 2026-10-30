{{-- Karte einer Quali auf der Übersicht: Person, Status, Rückmeldungen, Links je nach Rolle --}}
@props(['survey', 'aktUser', 'postCount' => 0])
@php
    $user = $survey->campuser->user;
    $leaders = $survey->campuser->leaders;
    $otherLeaders = $leaders->reject(fn ($leader) => $leader->is($aktUser))->sortBy('username', SORT_NATURAL | SORT_FLAG_CASE)->pluck('username')->implode(', ');
    $isParticipant = $aktUser->isTeilnehmer();
    $closed = $isParticipant && $survey->status()->isClosedByParticipant();
    $avatar = $survey->campuser->avatarPath();
    $initials = collect(preg_split('/\s+/', trim($user->username)))->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp
<article class="flex flex-col rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-600 shadow-xs dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
    <div class="flex items-start gap-3">
        @if ($avatar)
            <img src="{{ $avatar }}" alt="" class="size-10 shrink-0 rounded-full object-cover" loading="lazy">
        @else
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200" aria-hidden="true">{{ $initials }}</span>
        @endif
        <div class="min-w-0 flex-1">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                @if ($isParticipant)
                    {{ $user->username }}
                @else
                    <a href="{{ route('home.profile', $user->slug) }}" class="hover:underline">{{ $user->username }}</a>
                @endif
                <span class="font-normal text-gray-500 dark:text-gray-400">{{ $user->group['shortname'] ?? '' }}</span>
            </h3>
            {{-- Unter dem Namen, weil die Status-Namen lang sein können --}}
            <x-surveys.status-badge :survey="$survey" class="mt-1 inline-block"/>
            @if (! $isParticipant)
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ collect([
                        match (true) {
                            $leaders->isEmpty() => 'Noch ohne Leitende',
                            $otherLeaders === '' => null,
                            $leaders->contains($aktUser) => 'Zusammen mit '.$otherLeaders,
                            default => 'Leitende: '.$otherLeaders,
                        },
                        $postCount === 1 ? '1 Rückmeldung' : $postCount.' Rückmeldungen',
                    ])->filter()->implode(' · ') }}
                </p>
            @endif
        </div>
    </div>
    <div class="mt-3 flex flex-wrap gap-2">
        @if ($closed)
            <a href="{{ route('survey.compare', $survey->slug) }}" class="button-small">{{ $survey->SurveyName() }}</a>
        @elseif ($survey->TNIsAllowed())
            <a href="{{ route('survey.survey', $survey->slug) }}" class="button-small">{{ $survey->SurveyName() }}</a>
        @elseif ($isParticipant)
            <span class="text-gray-500 dark:text-gray-400">{{ $survey->SurveyName() }} ist noch nicht freigegeben</span>
        @endif
        @if (! $isParticipant)
            <a href="{{ route('survey.compare', $survey->slug) }}" class="button-small">Vergleich</a>
            <a href="{{ route('home.profile', $user->slug) }}?neu=1" class="button-small"><i class="fas fa-plus" aria-hidden="true"></i> Rückmeldung</a>
        @endif
    </div>
</article>
