@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($question, ['method' => 'Patch', 'action'=>['AdminQuestionsController@update',$question->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('chapter_id', 'Kapitel:') !!}
                        {!! Form::select('chapter_id', [''=>'Wähle Kapitel'] + $chapters, null,  ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                            {!! Form::label('number', 'Nummer:') !!}
                            {!! Form::text('number', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                            {!! Form::label('competence', 'Kompetenz:') !!}
                            {!! Form::text('competence', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('sort-index', 'Index zur Anzeige im Radar (Im Uhrzeigensinn ab 12Uhr):') !!}
                        {!! Form::text('sort-index', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Update Kompetenz', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}

                @if (Auth::user()->isAdmin())
                    {!! Form::model($question, ['method' => 'DELETE', 'action'=>['AdminQuestionsController@destroy',$question->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Kompetenz löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                @endif
                </div>
        </div>
    </div>
@endsection
