@extends('layouts.layout')
@section('survey_content')
    <div>
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        @if ($errors->camp_types->any())
            <x-layouts.alert type="danger">
            <ul class="list-disc ps-4">
                @foreach ($errors->camp_types->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-layouts.alert>
        @endif
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                {!! Form::open(['action'=>'CampTypesController@store']) !!}
                <div class="mb-2.5">
                    {!! Form::label('name', 'Name:', ['class' => 'field-label']) !!}
                    {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::submit('Kurs-Typ erstellen', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
@endsection
