@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help" :header=false/>
        <div class="row">
            <div class="col-md-4">
                Rückmeldung erstellen:
                {!! Form::open(['method' => 'POST', 'action'=>'PostController@store',  'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::hidden('post_id', null, ['id' => 'post_id']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment', 'Rückmeldung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::textarea('comment', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', 'Benutzer:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::select('user_id', [''=>'Wähle Teilnehmende'] + $users_select, null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            {!! Form::label('file', 'Datei:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::file('file', ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-hidden dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::checkbox('show_on_survey', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-xs focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                            {!! Form::label('show_on_survey', 'Sichtbar für Qualifikation', [ 'class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Rückmeldung Erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="col-md-8">
                <x-post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'"/>
                <br>
                <x-post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'"/>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('home.post_delete')
@endpush
