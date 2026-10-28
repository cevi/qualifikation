@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($survey, ['method' => 'PATCH', 'action'=>['AdminSurveysController@update', $survey->id]]) !!}

                <div class="mb-2.5">
                    {!! Form::label('survey_status_id', 'Status:', ['class' => 'field-label']) !!}
                    {!! Form::select('survey_status_id', [''=>'Wähle Status'] + $survey_statuses_id, $survey->survey_status_id?->value,  ['class' => 'field-input']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::checkbox('update', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-xs focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                    {!! Form::label('update', 'Allfällige Einträge löschen',  ['class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::submit('Änderungen speichern', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}

                @include('includes.form_error')

                
                {!! Form::open(['method' => 'DELETE', 'action'=>['AdminSurveysController@destroy',$survey]]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Lösche Qualifikation', ['class' => 'button-danger'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection
