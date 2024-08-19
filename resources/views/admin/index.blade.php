@extends('layouts.admin')

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    <x-page-title :title="$title" :help="$help" :header=false/>
    <!-- Counts Section -->
    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <!-- Count item widget-->
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="wrapper count-title d-flex">
                        <div class="icon"><i class="icon-padnote"></i></div>
                        <div class="name"><strong class="text-uppercase">Total Qualifikationsprozesse</strong>
                            <div class="count-number">{{$surveys_all ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="wrapper count-title d-flex">
                        <div class="icon"><i class="icon-padnote"></i></div>
                        <div class="name"><strong class="text-uppercase">1. Einschätzung ausgefüllt</strong>
                            <div class="count-number">{{$surveys_1offen ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="wrapper count-title d-flex">
                        <div class="icon"><i class="icon-padnote"></i></div>
                        <div class="name"><strong class="text-uppercase">2. Einschätzung ausgefüllt</strong>
                            <div class="count-number">{{$surveys_2offen ?? ''}}</div>
                        </div>
                    </div>
                </div>
                <!-- Count item widget-->
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="wrapper count-title d-flex">
                        <div class="icon"><i class="icon-padnote"></i></div>
                        <div class="name"><strong class="text-uppercase">Quali-Prozesse abgeschlossen</strong>
                            <div class="count-number">{{$surveys_fertig ?? ''}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <div class="row">
        <section class="col-lg-10 dashboard-header section-padding">
            <div class="container-fluid">
                <div
                    class="grid mb-8 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-12 md:grid-cols-2">
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
            </div>
        </section>
        <section class="col-lg-2 dashboard-header section-padding">
            <div class="container-fluid" id="Sidepanel">
                <div class="row d-flex align-items-md-stretch">
                    <ul class="list-unstyled">
                        @foreach ($surveys as $survey)
                            <li><a href="#Chart-{{$loop->iteration}}">{{$survey->campuser->user['username']}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    </div>

@endsection


@push('scripts')
    @include('home.radar')
    <script type="module">
        var $sidebar = $("#Sidepanel"),
            $window = $(window),
            offset = $sidebar.offset(),
            topPadding = 20;
        $window.scroll(function () {
            if ($window.scrollTop() > offset.top) {
                $sidebar.stop().animate({
                    marginTop: $window.scrollTop() - offset.top + topPadding
                });
            } else {
                $sidebar.stop().animate({
                    marginTop: 0
                });
            }
        });
    </script>
@endpush

