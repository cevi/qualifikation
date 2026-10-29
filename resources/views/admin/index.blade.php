@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
    </div>
    <!-- Counts Section -->
    <section class="dashboard-counts section-padding">
        <div class="px-[15px] md:px-12">
            <div class="flex flex-wrap -mx-[15px]">
                <!-- Count item widget-->
                <div class="px-[15px] md:w-1/4 w-1/2 max-[992px]:mb-5">
                    <div class="wrapper count-title flex">
                        <div class="name min-w-0"><strong class="text-uppercase hyphens-auto wrap-break-word">Total Qualifikations&shy;prozesse</strong>
                            <div class="count-number">{{$surveys_all ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="px-[15px] md:w-1/4 w-1/2 max-[992px]:mb-5">
                    <div class="wrapper count-title flex">
                        <div class="name min-w-0"><strong class="text-uppercase hyphens-auto wrap-break-word">1. Einschätzung ausgefüllt</strong>
                            <div class="count-number">{{$surveys_1offen ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="px-[15px] md:w-1/4 w-1/2 max-[992px]:mb-5">
                    <div class="wrapper count-title flex">
                        <div class="name min-w-0"><strong class="text-uppercase hyphens-auto wrap-break-word">2. Einschätzung ausgefüllt</strong>
                            <div class="count-number">{{$surveys_2offen ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="px-[15px] md:w-1/4 w-1/2 max-[992px]:mb-5">
                    <div class="wrapper count-title flex">
                        <div class="name min-w-0"><strong class="text-uppercase hyphens-auto wrap-break-word">Quali-Prozesse abgeschlossen</strong>
                            <div class="count-number">{{$surveys_fertig ?? ''}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <div class="flex flex-wrap -mx-[15px]">
        <section class="w-full lg:w-10/12 dashboard-header section-padding">
            <div class="px-[15px] md:px-12">
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
                                        {{$survey->campuser->user['username']}} {{$survey->campuser->user->group['shortname'] ?? ''}} - {{$survey->survey_status['name']}}
                                    </h3>
                                </a>
                            </blockquote>
                            <canvas id="radarChart-{{$loop->iteration}}" width="100%"
                                    height="100%"></canvas>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="hidden w-full lg:block lg:w-1/6 dashboard-header section-padding">
            <div class="px-[15px] md:px-12 lg:sticky lg:top-24" id="Sidepanel">
                <div class="flex flex-wrap -mx-[15px] md:items-stretch">
                    <ul class="list-none ps-0">
                        @foreach ($surveys as $survey)
                            <li><a href="#Chart-{{$loop->iteration}}">{{$survey->campuser->user['username']}} {{$survey->campuser->user->group['shortname'] ?? ''}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    </div>

@endsection


@push('scripts')
    @include('home.radar')
@endpush

