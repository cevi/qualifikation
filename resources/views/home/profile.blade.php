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
                                    <x-radar-chart/>
                                @endforeach
                            </div>
                            <div class="col-lg-6">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <x-post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'"/>
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
                                        <div class="form-row">
                                            <div class="col-md-6 form-group">
                                                {!! Form::label('file', 'Datei:') !!}
                                                {!! Form::file('file') !!}
                                            </div>
                                            <div class="col-md-6 form-group">
                                                {!! Form::label('show_on_survey', 'Sichtbar für Qualifikation:') !!}
                                                {!! Form::checkbox('show_on_survey', '1', false) !!}
                                            </div>
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
            $('#post_id').val(post['id']);
            $('#comment').val(post['comment']);
            $('#show_on_survey').prop("checked", post['show_on_survey']);
        }
    </script>
@endsection

