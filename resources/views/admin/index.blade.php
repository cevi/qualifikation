@extends('layouts.admin')

@section('content')
<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
       <!-- Counts Section -->
	<section class="dashboard-counts section-padding">
        <div class="container-fluid">
          <div class="row">
            <!-- Count item widget-->
            <div class="col-xl-4 col-md-4 col-6">
                <div class="wrapper count-title d-flex">
                  <div class="icon"><i class="icon-padnote"></i></div>
                  <div class="name"><strong class="text-uppercase">Total Stao-Gespräche</strong>
                    <div class="count-number">{{$surveys_all ?? ''}}</div>
                  </div>
                </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-4 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">Stao ausgefüllt</strong>
                    <div class="count-number">{{$surveys_abgeschlossen ?? ''}}</div>
                </div>
              </div>
            </div>
            <!-- Count item widget-->
            <div class="col-xl-4 col-md-4 col-6">
              <div class="wrapper count-title d-flex">
                <div class="icon"><i class="icon-padnote"></i></div>
                <div class="name"><strong class="text-uppercase">Stao-Gespräche geführt</strong>
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
        <section class="col-lg-2 dashboard-header section-padding" id="Sidepanel">
            <div class="container-fluid">
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
    <script>
        // $(document).scroll(function() { 
        //     var top = $(this).scrollTop();
        //     console.log(top);
        //     if(top >160){
        //         top = top - 165;
        //     }else{
        //         top=0;
        //     }
        //     $('#Sidepanel').animate({top:top},1, 'swing');
        // })

        $(document).ready(function () {

            'use strict';

            var brandleader = 'rgba(51, 179, 90, 0.2)';
            var branduser = 'rgba(179,181,198,0.2)';

            var surveys = @json($surveys);
            // surveys.forEach(function(survey, i) {
            for (var [i, survey] of Object.entries(surveys)){
                var RADARCHART  = $('#radarChart-'+(parseInt(i)+1));

                var labelstring = [];
                var datapoints = [];
                var datasets = [];
                var datapoints_leader = [];

                
     

                survey.chapters.forEach(chapter => {
                    var questions = chapter.questions;
                    questions.forEach(question => {
                        labelstring.push(question.question.competence);
                        datapoints.push(question.answer.count);
                        datapoints_leader.push(question.answer_leader.count);
                    });
                }); 

                datasets.push({
                                label: survey.user.username,
                                backgroundColor: branduser,
                                borderWidth: 2,
                                borderColor: branduser,
                                pointBackgroundColor: branduser,
                                pointBorderColor: "#fff",
                                pointHoverBackgroundColor: "#fff",
                                pointHoverBorderColor: branduser,
                                data: datapoints
                            });
                    datasets.push({
                                label: survey.responsible.username,
                                backgroundColor: brandleader,
                                borderWidth: 2,
                                borderColor: brandleader,
                                pointBackgroundColor: brandleader,
                                pointBorderColor: "#fff",
                                pointHoverBackgroundColor: "#fff",
                                pointHoverBorderColor: brandleader,
                                data: datapoints_leader
                            });     

                var radarChart = new Chart(RADARCHART, {
                    type: 'radar',
                    data: {
                        labels:  labelstring,
                        datasets: datasets
                    },
                    options: {
                        scale : {
                            ticks: {
                                min: -2,
                                max: 2,
                                maxTicksLimit:5,
                            }
                        }
                    }
                });
                var radarChart = {
                    responsive: true
                };
            };
        });
    </script>
@endsection

