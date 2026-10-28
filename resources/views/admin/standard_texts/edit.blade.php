@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($standard_text, ['method' => 'Patch', 'action'=>['StandardTextsController@update',$standard_text]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('title', 'Title:', ['class' => 'field-label']) !!}
                        {!! Form::text('title', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                <div class="mb-2.5">
                    {!! Form::label('content', 'Inhalt:', ['class' => 'field-label']) !!}
                    {!! Form::textarea('content', null,  ['rows' => 10, 'class' => 'field-input', 'required']) !!}
                </div>
                
                <div class="mb-2.5">
                    {!! Form::submit('Standard-Text aktualisieren', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}

                {!! Form::open(['method' => 'DELETE', 'action'=>['StandardTextsController@destroy',$standard_text]]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Lösche Standard-Text', ['class' => 'button-danger'])!!}
                </div>
                {!! Form::close()!!}
        </div>
    </div>
@endsection