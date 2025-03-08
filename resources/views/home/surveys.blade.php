@extends('layouts.layout')

@section('survey_content')
    <x-page-title :title="$title" :help="$help" :header=false/>
    @if ($surveys)
        <div class="card table-responsive">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        @if ($aktUser->isTeilnehmer() || ($aktUser->isLeader()))
                            <th scope="col" class="px-6 py-3">Name</th>
                        @endif
                        @if (!$aktUser->isTeilnehmer())
                            <th scope="col" class="px-6 py-3">Vergleich</th>
                        @endif
                        <th scope="col" class="px-6 py-3">Teilnehmer</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                @if ($surveys)
                    @foreach ($surveys as $survey)
                        {{-- @if($survey->MySurvey() || $aktUser->isCampleader()) --}}
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            @if (($aktUser->isTeilnehmer() && $survey['survey_status_id'] < config('status.survey_tnAbgeschlossen')) ||
                                $aktUser->isLeader())
                                <td class="px-6 py-4">
                                    @if ($survey->TNisAllowed())
                                        <a href="{{route('survey.survey', $survey->slug)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$survey->SurveyName()}}</a>
                                    @else
                                        {{$survey->SurveyName()}}
                                    @endif
                                </td>
                            @endif
                            @if ($aktUser->isTeilnehmer() && $survey['survey_status_id'] >= config('status.survey_tnAbgeschlossen'))
                                <td class="px-6 py-4"><a href="{{route('survey.compare', $survey->slug)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$survey->SurveyName()}}</a>
                                </td>
                            @endif
                            @if (!$aktUser->isTeilnehmer())
                                <td class="px-6 py-4"><a href="{{route('survey.compare', $survey->slug)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Vergleich</a></td>
                            @endif
                            <td>
                                @if (!$aktUser->isTeilnehmer())
                                    <a href="{{route('home.profile', $survey->campuser->user->slug)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$survey->campuser->user['username']}}</a>
                                @else
                                    {{$survey->campuser->user['username']}}
                                @endif
                            </td>
                            <td class="px-6 py-4">{{$survey->survey_status['name']}}</td>
                        </tr>
                        {{-- @endif --}}
                    @endforeach

                @endif

                </tbody>
            </table>
        </div>
        @if ($aktUser->isLeader())
            <div
                class="grid mb-8 border border-gray-200 rounded-lg shadow-2xs dark:border-gray-700 md:mb-12 md:grid-cols-2">

                @foreach($surveys as $survey)
                    <figure
                        class="flex flex-col items-center justify-center p-8 text-center border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-tl-lg md:border-r dark:bg-gray-800 dark:border-gray-700"
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
