@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($competence, ['method' => 'Patch', 'action'=>['AdminCompetencesController@update',$competence->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Beschreibung:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('question_id', 'Kompetenz:', ['class' => 'field-label']) !!}
                        {!! Form::select('question_id', [''=>'Wähle Kompetenz'] + $questions, null,  ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('camp_type_id', 'Kurs-Typ:', ['class' => 'field-label']) !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurs-Typ'] + $camp_types, null,  ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Änderungen speichern', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}

                {!! Form::model($competence, ['method' => 'DELETE', 'action'=>['AdminCompetencesController@destroy',$competence->id]]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Kernkompetenz löschen', ['class' => 'button-danger'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>    
@endsection