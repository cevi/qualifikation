@extends('layouts.admin')

@section('content')
        <div class="px-4 md:px-12">
            <!-- Page Header-->
            <x-layouts.page-title :title="$title" :help="$help"/>
            <div class="grid grid-cols-12 sm:gap-x-8">
                <div class="min-w-0 col-span-12 sm:col-span-6">
                    {!! Form::model($answer, ['method' => 'Patch', 'action'=>['AdminAnswersController@update',$answer->id]]) !!}
                        <div class="mb-2.5">
                            {!! Form::label('name', 'Antwort:', ['class' => 'field-label']) !!}
                            {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                        </div>
                        <div class="mb-2.5">
                            {!! Form::label('count', 'Wertung:', ['class' => 'field-label']) !!}
                            {!! Form::text('count', null, ['class' => 'field-input', 'required']) !!}
                        </div>
                        <div class="mb-2.5">
                            {!! Form::label('description', 'Beschreibung:', ['class' => 'field-label']) !!}
                            {!! Form::text('description', null, ['class' => 'field-input', 'required']) !!}
                        </div>
                        <div class="mb-2.5">
                            {!! Form::submit('Update Antwort', ['class' => 'button-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($answer, ['method' => 'DELETE', 'action'=>['AdminAnswersController@destroy',$answer->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::submit('Antwort löschen', ['class' => 'button-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
@endsection