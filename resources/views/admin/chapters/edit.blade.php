@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($chapter, ['method' => 'Patch', 'action'=>['AdminChaptersController@update',$chapter->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('number', 'Nummer:', ['class' => 'field-label']) !!}
                        {!! Form::text('number', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Kapitel:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Update Kapitel', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}

                {!! Form::model($chapter, ['method' => 'DELETE', 'action'=>['AdminChaptersController@destroy',$chapter->id]]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Kapitel löschen', ['class' => 'button-danger'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>    
@endsection