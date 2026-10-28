@extends('layouts.layout')

@section('survey_content')
    <x-layouts.page-title :title="$title" :help="$help" :header=false/>
    @if ($surveys)
        @php
            // Pro Quali: Name mit Link (Ausfüllen bzw. nach dem Abschliessen Vergleich), Vergleich, Person
            $rows = collect($surveys)->map(function ($survey) use ($aktUser) {
                $closed = $aktUser->isTeilnehmer() && $survey->status()->isClosedByParticipant();
                $user = $survey->campuser->user;

                return [
                    'name' => $survey->SurveyName(),
                    'nameUrl' => $closed ? route('survey.compare', $survey->slug) : ($survey->TNisAllowed() ? route('survey.survey', $survey->slug) : null),
                    'compareUrl' => $aktUser->isTeilnehmer() ? null : route('survey.compare', $survey->slug),
                    'person' => trim($user['username'].' '.($user->group['shortname'] ?? '')),
                    'personUrl' => $aktUser->isTeilnehmer() ? null : route('home.profile', $user->slug),
                    'status' => $survey->survey_status['name'],
                ];
            });
            $showName = $aktUser->isTeilnehmer() || $aktUser->isLeader();
            $link = 'font-medium text-blue-600 dark:text-blue-500 hover:underline';
        @endphp
        {{-- Handy: eine Karte pro Quali --}}
        <div class="mb-8 space-y-3 md:hidden">
            @foreach ($rows as $row)
                <article class="rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-600 shadow-xs dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="min-w-0 text-base font-semibold text-gray-900 dark:text-white">
                            @if ($row['personUrl'])
                                <a href="{{ $row['personUrl'] }}" class="{{ $link }}">{{ $row['person'] }}</a>
                            @else
                                {{ $row['person'] }}
                            @endif
                        </h2>
                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ $row['status'] }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1">
                        @if ($showName)
                            @if ($row['nameUrl'])
                                <a href="{{ $row['nameUrl'] }}" class="{{ $link }}">{{ $row['name'] }}</a>
                            @else
                                <span>{{ $row['name'] }}</span>
                            @endif
                        @endif
                        @if ($row['compareUrl'])
                            <a href="{{ $row['compareUrl'] }}" class="{{ $link }}">Vergleich</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="relative mb-8 hidden overflow-x-auto rounded-lg border border-gray-200 shadow-xs md:block dark:border-gray-700">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        @if ($showName)
                            <th scope="col" class="px-6 py-3">Name</th>
                        @endif
                        @if (!$aktUser->isTeilnehmer())
                            <th scope="col" class="px-6 py-3">Vergleich</th>
                        @endif
                        <th scope="col" class="px-6 py-3">Teilnehmende</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            @if ($showName)
                                <td class="px-6 py-4">
                                    @if ($row['nameUrl'])
                                        <a href="{{ $row['nameUrl'] }}" class="{{ $link }}">{{ $row['name'] }}</a>
                                    @else
                                        {{ $row['name'] }}
                                    @endif
                                </td>
                            @endif
                            @if ($row['compareUrl'])
                                <td class="px-6 py-4"><a href="{{ $row['compareUrl'] }}" class="{{ $link }}">Vergleich</a></td>
                            @endif
                            <td class="px-6 py-4">
                                @if ($row['personUrl'])
                                    <a href="{{ $row['personUrl'] }}" class="{{ $link }}">{{ $row['person'] }}</a>
                                @else
                                    {{ $row['person'] }}
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $row['status'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($aktUser->isLeader())
            <div
                class="grid mb-8 border border-gray-200 rounded-lg shadow-2xs dark:border-gray-700 md:mb-12 md:grid-cols-2">

                @foreach($surveys as $survey)
                    <figure
                        class="flex flex-col items-center justify-center p-4 sm:p-8 text-center border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-tl-lg md:border-r dark:bg-gray-800 dark:border-gray-700"
                        id="Chart-{{$loop->iteration}}">
                        <!-- Recent Activities Widget      -->
                        <blockquote class="max-w-2xl mx-auto mb-4 text-gray-500 lg:mb-8 dark:text-gray-400">
                            <a href="{{route('survey.compare',$survey['slug'])}}" target="blank">

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{$survey->campuser->user['username']}} - {{$survey->survey_status['name']}}
                                </h3>
                            </a>
                        </blockquote>
                        <canvas id="radarChart-{{$loop->iteration}}" width="100%"
                                height="100%"></canvas>
                    </figure>
                @endforeach
            </div>
        @endif
    @endif

@endsection

@push('scripts')
    @if ($aktUser->isLeader())
        @include('home.radar')
    @endif
@endpush
