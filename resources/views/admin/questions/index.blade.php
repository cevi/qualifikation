@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-4">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminQuestionsController@store']) !!}
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
                    {!! Form::submit('Kompetenz Erstellen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
                @include('includes.form_error')
            </div>
            <div class="col-sm-8">
                @if ($questions)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Kapitel-Nr.</th>
                                <th scope="col">Kapitel</th>
                                <th scope="col">Nummer</th>
                                <th scope="col">Kompetenz</th>
                                <th scope="col">Beschreibung</th>
                                <th scope="col">Sort-Index</th>
                            </tr>
                        </thead>
                    @foreach ($questions as $question)
                        <tbody>
                            <tr>
                                <td>{{$question->chapter['number']}}</td>
                                <td>{{$question->chapter['name']}}</td>
                                <td>{{$question->number}}</td>
                                <td><a href="{{route('questions.edit',$question->id)}}">{{$question->competence}}</a></td>
                                <td>{{$question->name}}</td>
                                <td>{{$question['sort-index']}}</td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>

                @endif

            </div>
        </div>
    </div>
@endsection
