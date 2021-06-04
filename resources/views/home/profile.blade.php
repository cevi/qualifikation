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
                                    <img src="{{$user->avatar ? $user->avatar : '/img/default_avatar.svg'}}" alt="img" class="img-circle img-bor">
                                </div>
                            </div>
                            <div class="profile_user">
                                <h3 class="user_name_max">{{$user->username}}</h3>
                                <p>{{$user->leader ? $user->leader->username : ''}}</p>
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
                            @foreach($surveys as $survey) 
                                <div class="card-header d-flex align-items-center">
                                    <h4>Kompetenzendarstellung</h4>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="radarChart-1" width="100%" height="100%"></canvas>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <strong>Rückmeldungen</strong>
                                    <table class="table">
                                        <tbody>
                                            @foreach ($posts as $post)
                                                <tr>
                                                    <td style="width:75%">
                                                        {!! nl2br($post->comment) !!}
                                                    </td>
                                                    <td style="width:20%; text-align: right;">
                                                        {{$post->leader['username']}}<br>
                                                        {{$post->created_at ? $post->created_at->isoFormat('LL') : 'no date'}}<br>
                                                        @if ($post->file)
                                                            <a href="/{{$post->file}}" target="_blank">{{substr(basename($post->file, '.'.pathinfo($post->file, PATHINFO_EXTENSION)), 11)}}</a>    
                                                        @endif
                                                    </td>
                                                    <td style="width:5%; text-align: right;">
                                                        @if ($post->leader['id'] === Auth::user()->id)
                                                            {!! Form::model($post, ['method' => 'DELETE', 'action'=>['PostController@destroy',$post]]) !!}
                                                            <div class="form-group">
                                                               {!! Form::button(' <i class="fas fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-sm'])!!}
                                                            </div>
                                                            {!! Form::close()!!}
                                                            
                                                        @endif
                                                    </td>
                                                </tr>  
                                            @endforeach
                                        </tbody>
                                    </table>  
                                    <hr>
                                    {!! Form::open(['method' => 'POST', 'action'=>'PostController@store',  'files' => true]) !!}
                                        <div class="form-group">
                                            {!! Form::hidden('user_id', $user->id) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('comment', 'Rückmeldung:') !!}
                                            {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('file', 'Datei:') !!}
                                            {!! Form::file('file') !!}
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
    @include('home.radar')
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
    </script>
@endsection

