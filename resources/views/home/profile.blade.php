@extends('layouts.layout')
@section('survey_content')
@include('includes.tinyeditor')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-body cardbody-navtabs">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="text-center mbl">
                                    <img src="{{$user->avatar}}" alt="img" class="img-circle img-bor">
                                </div>
                            </div>
                            <div class="profile_user">
                                <h3 class="user_name_max">{{$user->username}}</h3>
                                <p>{{$user->leader->username}}</p>
                            </div>
                            <br> 
                            <div class="text-center mbl">
                                <div class="ampel" id="ampel">
                                    <a href="javascript:;" class="ampel-btn" data-color="{{config('status.classification_red')}}" data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_red')])}}' >
                                        <div class="circle {{$user->classification_id == config('status.classification_red') ? 'red' : ''}}" color="yellow"></div>
                                    </a>
                                    <a href="javascript:;" class="ampel-btn" data-color="{{config('status.classification_yellow')}}" data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_yellow')])}}'>
                                        <div class="circle {{$user->classification_id == config('status.classification_yellow') ? 'yellow' : ''}}" color="yellow"></div>
                                    </a>
                                    <a href="javascript:;" class="ampel-btn" data-color="{{config('status.classification_green')}}" data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_green')])}}'> 
                                        <div class="circle {{$user->classification_id == config('status.classification_green') ? 'green' : ''}}" color="red"></div>
                                    </a>
                                </div>   
                            </div>   
                        </div>
                        <div class="col-md-8">
                            @foreach($survey as $survey) 
                              <div class="card-header d-flex align-items-center">
                                <h4>Kompetenzendarstellung</h4>
                              </div>
                              <div class="card-body">
                                <div class="chart-container">
                                  <canvas id="radarChart"></canvas>
                                </div>
                            </div>
                            @endforeach
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <strong>Rückmeldungen</strong>
                                    <table class="table table-responsive">
                                        <tbody>
                                            @foreach ($posts as $post)
                                                <tr>
                                                    <td style="width:80%">
                                                        {!! nl2br($post->comment) !!}
                                                    </td>
                                                    <td style="width:20%">
                                                        {{$post->leader['username']}}<br>
                                                        {{$post->created_at ? $post->created_at->diffForHumans() : 'no date'}}
                                                    </td>
                                                </tr>  
                                            @endforeach
                                        </tbody>
                                    </table>  
                                    <hr>
                                    {!! Form::open(['method' => 'POST', 'action'=>'PostController@store']) !!}
                                        <div class="form-group">
                                            {!! Form::hidden('user_id', $user->id) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('comment', 'Rückmeldung:') !!}
                                            {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::submit('Rückmeldung Erstellen', ['class' => 'btn btn-primary'])!!}
                                        </div> 
                                    {!! Form::close()!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
    <script>
        $('.ampel-btn').on('click', function () { 
            $.ajaxSetup({
            headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var url = $(this).data('remote');
            var color = $(this).data('color');
            $.ajax({
                url: url,
                type: 'PATCH',
                data:{},
                success: function(res) {
                    location.reload();
                }
            });
        });
        $(document).ready(function () {
            'use strict';

            var brandleader = 'rgba(51, 179, 90, 0.2)';
            var branduser = 'rgba(179,181,198,0.2)';

            var survey = @json($survey);
            // surveys.forEach(function(survey, i) {
            var RADARCHART  = $('#radarChart');

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
        });
    </script>
@endsection

