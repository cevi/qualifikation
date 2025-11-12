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
                                <h3 class="user_name_max">{{$user->username}} {{$user->group['shortname'] ?? ''}}</h3>
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
                                    <x-post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :user="$user"/>
                                    {!! Form::model($post_new, ['method' => 'POST', 'action'=>['UsersController@storePost', $user],  'files' => true]) !!}
                                    <div class="form-group">
                                        {!! Form::hidden('post_id', $post_new['id']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('comment',  'Rückmeldung (// für Vorgaben eingeben):', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                        {!! Form::textarea('comment', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 3]) !!}
                                    </div>
                                    @if($post_new->file)
                                        <div class="form-group">
                                            {!! Form::checkbox('delete_file', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-xs focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                                            {!! Form::label('delete_file', 'Bestehende Datei "' . $post_new->filename(). '" löschen', ['class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                                        </div>
                                        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">oder<p>
                                    @endif
                                    <div class="form-group">
                                        {!! Form::file('file',  null,['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-hidden dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::checkbox('show_on_survey', '1', $post_new['show_on_survey'], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-xs focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                                        {!! Form::label('show_on_survey', 'Sichtbar für Qualifikation', ['class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::submit('Rückmeldung Erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800'])!!}
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

