@extends('layouts.layout')

@section('survey_content')
     <!-- Blog Post -->

    <!-- Title -->
    <h1>Vergleich</h1>

    <!-- Author -->
    <p class="lead">
        von {{$survey->user['username']}}
    </p>
    @foreach ($survey->chapters as $chapter)
        <div id="recent-activities-wrapper-{{$chapter->chapter['number']}}" class="card updates activities">
            <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 display">
                <a data-toggle="collapse" data-parent="#recent-activities-wrapper-{{$chapter->chapter['number']}}" href="#activities-box-{{$chapter->chapter['number']}}" aria-expanded="true" aria-controls="activities-box">
                    {{$chapter->chapter['number']}}. {{$chapter->chapter['name']}}
                    </a>
                </h2>
                <a data-toggle="collapse" data-parent="#recent-activities-wrapper-{{$chapter->chapter['number']}}" href="#activities-box-{{$chapter->chapter['number']}}" aria-expanded="true" aria-controls="activities-box">
                <i class="fa fa-angle-down">
                    </i>
                </a>
            </div>
            <div id="activities-box-{{$chapter->chapter['number']}}" role="tabpanel" class="collapse">

                        @foreach ($chapter->questions as $question)  
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td width="50px">{{$question->question['number']}}</td>
                                        <td width="150px">{{$question->question['competence']}}</td>
                                        <td width="300px">{{$question->question['name']}}</td>
                                        <td width="50px">{{$question->answer['name']}}</td>
                                        <td width="200px">{{$question['comment']}}</td>
                                        <td width="50px">{{$question->answer_leader['name']}}</td>
                                        <td width="200px">{{$question['comment_leader']}}</td> 
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach  
                    
            </div>
        </div>  
        @endforeach
        <div class="card radar-chart-example">
          <div class="card-header d-flex align-items-center">
            <h4>Kompetenzendarstellung</h4>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="radarChart"></canvas>
            </div>
          </div>
        </div>

   
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            'use strict';

            var brandleader = 'rgba(51, 179, 90, 0.2)';
            var branduser = 'rgba(179,181,198,0.2)';

            var RADARCHART  = $('#radarChart');

            var labelstring = [];
            var datapoints = [];
            var datasets = [];
            var datapoints_leader = [];
            var survey = @json($survey);
            var chapters = @json($survey->chapters);
            var user = @json($user);

            chapters.forEach(chapter => {
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
            if(@json(($user->isleader()) || $user->iscampleader())){
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
            }

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
        });
    </script>
@endsection