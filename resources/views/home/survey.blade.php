@extends('layouts.layout')

@section('survey_content')
<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>
     <!-- Blog Post -->

    <!-- Title -->
    <h1>{{$survey['name']}}</h1>

    <!-- Author -->
    <p class="lead">
        von {{$survey->user['username']}}
    </p>

    
    {!! Form::model($survey, ['method' => 'Patch', 'action'=>['SurveysController@update',$survey->id]]) !!}
    @foreach ($survey->chapters as $chapter)
        <div id="recent-activities-wrapper-{{$chapter->chapter['number']}}" class="card updates activities">
            <a data-toggle="collapse" data-parent="#recent-activities-wrapper-{{$chapter->chapter['number']}}" href="#activities-box-{{$chapter->chapter['number']}}" aria-expanded="true" aria-controls="activities-box">
                <div id="activites-header" class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 display">
                        {{$chapter->chapter['number']}}. {{$chapter->chapter['name']}}
                    </h2>
                    <i class="fa fa-angle-down"></i>
                </div> 
            </a>

            <div id="activities-box-{{$chapter->chapter['number']}}" role="tabpanel" class="collapse">

                        @foreach ($chapter->questions as $question)  
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td width="50px">{{$question->question['number']}}</td>
                                    <td width="150px">{{$question->question['competence']}}</td>
                                    <td width="300px">{{$question->question['name']}}</td>
                                </tr>
                                </tbody>
            
                            </table>
                            <div class="form-group row" style="padding-left:10px; padding-right:10px"> 
                                <div class="col-sm-12">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                @foreach ($answers as $answer)  
                                                    @if($user->isLeader())
                                                        <td width="50px">
                                                            {{ Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_leader_id']===NULL && $answer['name']==='0') ? true : ($question['answer_leader_id']===$answer['id']) ? true : false, ["id" => $question->question['number'].$answer['id']]) }}
                                                            {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                        </td>
                                                    @else
                                                        <td width="50px">
                                                            {{Form::radio('answers['.$question['id'].']', $answer['id'],  ($question['answer_id']===NULL && $answer['name']==='0') ? true : ($question['answer_id']===$answer['id']) ? true : false, ["id" => $question->question['number'].$answer['id']]) }}
                                                            {!! Form::label($question->question['number'].$answer['id'], $answer['name'] ? $answer['name'] : " 0 ") !!}
                                                        </td>
                                                    @endif
                                                @endforeach  
                                            </tr>
                                        </tbody>
                    
                                    </table>
                                </div>
                                <div class="col-sm-2"> 
                                    @if($user->isLeader())
                                        {!! Form::label('comment_leader', 'Kommentar:') !!}
                                    @else
                                        {!! Form::label('comment', 'Kommentar:') !!}
                                    @endif
                                </div>
                                <div class="col-sm-10">
                                    @if($user->isLeader())
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment_leader'], ['class' => 'form-control', 'rows'=> 2]) !!}
                                    @else
                                        {!! Form::textarea('comments['.$question['id'].']', $question['comment'], ['class' => 'form-control', 'rows'=> 2]) !!}
                                    @endif
                                     </div>
                            </div>
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
          <div class="card-footer d-flex align-items-center">
            Die Grafik wird erst nach dem Speichern aktualisiert.
          </div>
        </div>
    <div class="form-group">
        {!! Form::submit('Quali Speichern', ['class' => 'btn btn-primary'])!!}
    </div>
    {!! Form::close()!!}

   
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
            if(@json($user->isleader())){
                datasets.push({
                            label: user.username,
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