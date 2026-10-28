@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($question, ['method' => 'Patch', 'action'=>['AdminQuestionsController@update',$question->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('chapter_id', 'Kapitel:', ['class' => 'field-label']) !!}
                        {!! Form::select('chapter_id', [''=>'Wähle Kapitel'] + $chapters, null,  ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                            {!! Form::label('number', 'Nummer:', ['class' => 'field-label']) !!}
                            {!! Form::text('number', null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                        {!! Form::label('name', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                            {!! Form::label('competence', 'Kompetenz:', ['class' => 'field-label']) !!}
                            {!! Form::text('competence', null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                        {!! Form::label('sort-index', 'Index zur Anzeige im Radar (Im Uhrzeigensinn ab 12Uhr):', ['class' => 'field-label']) !!}
                        {!! Form::text('sort-index', null, ['class' => 'field-input', 'required']) !!}
                    </div>

                    <div class="mb-2.5">
                        {!! Form::submit('Update Kompetenz', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}

                @if (Auth::user()->isAdmin())
                    {!! Form::model($question, ['method' => 'DELETE', 'action'=>['AdminQuestionsController@destroy',$question->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::submit('Kompetenz löschen', ['class' => 'button-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                @endif
                </div>
        </div>
    </div>
@endsection
