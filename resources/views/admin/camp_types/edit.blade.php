@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($camp_type, ['method' => 'Patch', 'action'=>['CampTypesController@update',$camp_type]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('name', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Änderungen speichern', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                {!! Form::close()!!}
                @if($camp_type->camps->count()==0)
                    {!! Form::model($camp_type, ['method' => 'DELETE', 'action'=>['CampTypesController@destroy',$camp_type], 'id'=> "DeleteForm"]) !!}
                        <div class="form-group">
                            {!! Form::submit('Kurs löschen', ['class' => 'focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'])!!}
                        </div>
                    {!! Form::close()!!}
                @endif
                </div>
        </div>
    </div>
@endsection