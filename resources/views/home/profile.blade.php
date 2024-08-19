@extends('layouts.layout')
@section('survey_content')
    {{--@include('includes.tinyeditor')--}}
    <div class="row">
        <x-page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
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
                                    <img src="{{$user->getAvatar()}}" alt="img"
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
                                        {!! Form::hidden('user_id', $user->id, ['id' => 'post_id']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('comment', 'Rückmeldung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                        {!! Form::textarea('comment', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 3]) !!}
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('file', 'Datei:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                            {!! Form::file('file', ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                                        </div>
                                        <div class="col-md-6 form-group">
                                            {!! Form::checkbox('show_on_survey', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                                            {!! Form::label('show_on_survey', 'Sichtbar für Qualifikation', [ 'class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::submit('Rückmeldung Erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
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
@endsection


@push('scripts')
    @include('home.radar')
    @include('home.post_delete')
    <script type="module">
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
    </script>
@endpush

