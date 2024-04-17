@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($survey, ['method' => 'PATCH', 'action'=>['AdminSurveysController@update', $survey->id]]) !!}

                <div class="form-group">
                    {!! Form::label('survey_status_id', 'Status:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('survey_status_id', [''=>'Wähle Status'] + $survey_statuses_id, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>

                <div class="form-group">
                    {!! Form::checkbox('update', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                    {!! Form::label('update', 'Allfällige Einträge löschen',  ['class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Änderungen speichern', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}

                @include('includes.form_error')
            </div>
        </div>
    </div>
@endsection
