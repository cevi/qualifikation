@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camps->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->camps->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'CampsController@store']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::text('name', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            {!! Form::label('camp_type_id', 'Kurstyp:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        </div>
                        <div class="col-auto">
                            <a tabindex="-1" class="form-text small text-muted"
                                href="{{ route('camp_types.create') }}">
                                Eigenen Kurstyp erstellen?
                            </a>
                        </div>
                    </div>
                    {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::checkbox('status_control', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                    {!! Form::label('status_control', 'Quali-Ablauf kontrollieren', [ 'class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('end_date', 'Schlussdatum:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::date('end_date', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('group_id', 'Organisierende Gruppe:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                @if (config('app.import_db'))
                    <div class="form-group">
                        {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::submit('Kurs erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection
