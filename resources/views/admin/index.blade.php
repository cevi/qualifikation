@extends('layouts.admin')

@section('content')
    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
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
                <div class="name"><strong class="text-uppercase">1. Selbsteinschätzung ausgefüllt</strong>
                    <div class="count-number">{{$surveys_1offen ?? ''}}</div>
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-3 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">2. Selbsteinschätzung ausgefüllt</strong>
                    <div class="count-number">{{$surveys_2offen ?? ''}}</div>
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-3 col-md-3 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">Qualifikationsprozesse abgeschlossen</strong>
                    <div class="count-number">{{$surveys_fertig ?? ''}}</div>
                </div>
              </div>
            </div>
            
    </section>
    <div class="row">
        <section class="col-lg-10 dashboard-header section-padding">
            <div class="container-fluid">
                <div class="row d-flex align-items-md-stretch"> 
                    @foreach($surveys as $survey) 
                        <div class="col-lg-6 col-md-4" id="Chart-{{$loop->iteration}}">
                            <!-- Recent Activities Widget      -->
                            <div class="card updates activities">
                                <a href="{{route('survey.compare',$survey->user_id)}}" target="blank">
                                    <div  class="card-header d-flex justify-content-between align-items-center">
                                        <h2 class="h5 display">{{$survey->user['username']}}</h2>
                                        <h2 class="h5 display">{{$survey->survey_status['name']}}</h2>
                                    </div>
                                </a>
                                <div role="tabpanel" class="collapse show">
                                    <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="radarChart-{{$loop->iteration}}"></canvas>
                                    </div>
                                    </div>
                                </div>  
                            </div>
                        </div>     
                    @endforeach
                </div>
            </div>
        </section>
        <section class="col-lg-2 dashboard-header section-padding">
            <div class="container-fluid"  id="Sidepanel">
                <div class="row d-flex align-items-md-stretch"> 
                    <ul class="list-unstyled">
                        @foreach ($surveys as $survey)
                            <li><a href="#Chart-{{$loop->iteration}}">{{$survey->user['username']}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('scripts')
    @include('home.radar')
    <script>
        var     $sidebar   = $("#Sidepanel"), 
                $window    = $(window),
                offset     = $sidebar.offset(),
                topPadding = 20;
        console.log($sidebar);
        $window.scroll(function() {
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
@endsection

