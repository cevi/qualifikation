@extends('layouts.layout')
@section('survey_content')
    {{--@include('includes.tinyeditor')--}}
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-primary">
                    <div class="card-body cardbody-navtabs">
                        <p>
                            Diese Seite ist für Teilnehmer nicht sichtbar.
                        </p>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="text-center-profile mbl">
                                        <img src="{{$user->avatar ?  : '/img/default_avatar.svg'}}" alt="img"
                                             class="img-circle img-bor">
                                    </div>
                                </div>
                                <div class="profile_user">
                                    <h3 class="user_name_max">{{$user->username}}</h3>
                                    <p>{{$user->leader ? $user->leader->username : ''}}</p>
                                </div>
                                <br>
                                <div class="text-center-profile mbl">
                                    <div class="ampel" id="ampel">
                                        <a href="javascript:" class="ampel-btn"
                                           data-color="{{config('status.classification_red')}}"
                                           data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_red')])}}'>
                                            <div
                                                class="circle {{$camp_user->classification_id == config('status.classification_red') ? 'red' : ''}}"></div>
                                        </a>
                                        <a href="javascript:" class="ampel-btn"
                                           data-color="{{config('status.classification_yellow')}}"
                                           data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_yellow')])}}'>
                                            <div
                                                class="circle {{$camp_user->classification_id == config('status.classification_yellow') ? 'yellow' : ''}}"></div>
                                        </a>
                                        <a href="javascript:" class="ampel-btn"
                                           data-color="{{config('status.classification_green')}}"
                                           data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_green')])}}'>
                                            <div
                                                class="circle {{$camp_user->classification_id == config('status.classification_green') ? 'green' : ''}}"></div>
                                        </a>
                                    </div>
                                </div>
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
                            </div>
                            <div class="col-lg-6">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <strong>Rückmeldungen</strong>
                                        <div class="profile-table">
                                            @foreach ($posts as $post)
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                                                        {{$post->comment}}
                                                    </div>
                                                    <div class="col-lg-3 col-md-10 col-sm-10 col-xs-10 text-right">
                                                        <div class="row">
                                                            <div
                                                                class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                                                                {{$post->leader['username']}}
                                                            </div>
                                                            <div
                                                                class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                                                                {{$post->created_at ? $post->created_at->isoFormat('L') : 'no date'}}
                                                            </div>
                                                            @if ($post->file)
                                                                <div
                                                                    class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                                                                    <a href="/{{$post->file}}"
                                                                       target="_blank">{{substr(basename($post->file, '.'.pathinfo($post->file, PATHINFO_EXTENSION)), 11)}}</a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 text-right">
                                                        @if ($post->leader['id'] === Auth::user()->id)
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-12 col-md-6 col-sm-6 col-xs-6 text-right">
                                                                    {!! Form::model($post, ['method' => 'DELETE', 'action'=>['PostController@destroy',$post]]) !!}
                                                                    <div class="form-group">
                                                                        {!! Form::button(' <i class="fas fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-sm'])!!}
                                                                    </div>
                                                                    {!! Form::close()!!}
                                                                </div>
                                                                <div
                                                                    class="col-lg-12 col-md-6 col-sm-6 col-xs-6 text-right">
                                                                    <button class="btn btn-sm"
                                                                            onclick="editPost({{$post}})"><i
                                                                            class="fas fa-edit"></i></button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                        </div>
                                        {!! Form::open(['method' => 'POST', 'action'=>'PostController@store',  'files' => true]) !!}
                                        <div class="form-group">
                                            {!! Form::hidden('post_id', null, ['id' => 'post_id']) !!}
                                        </div>
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
                data: {},
                success: function (res) {
                    location.reload();
                }
            });
        });

        function editPost(post) {
            console.log(post);
            $('#post_id').val(post['id']);
            $('#comment').val(post['comment']);
        }
    </script>
@endsection

