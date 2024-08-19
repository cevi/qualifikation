@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'AdminCompetencesController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Beschreibung:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('question_id', 'Kompetenz:') !!}
                        {!! Form::select('question_id', [''=>'Wähle Kompetenz'] + $questions, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('camp_type_id', 'Kurs-Typ:') !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurs-Typ'] + $camp_types, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Kernkompetenzen erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
                @include('includes.form_error')
            </div>    
            <div class="col-sm-9">
                @if ($competences)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Kompetenz</th>
                                <th scope="col">Kurs-Typ</th>
                                <th scope="col">Erstellt am</th>
                                <th scope="col">Geändert am</th>
                            </tr>
                        </thead>
                        @foreach ($competences as $competence)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('competences.edit',$competence->id)}}">{{$competence->name}}</a></td>
                                    <td>{{$competence->question['name']}}</td>
                                    <td>{{$competence->camp_type['name']}}</td>
                                    <td>{{$competence->created_at ? $competence->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$competence->updated_at ? $competence->updated_at->diffForHumans() : 'no date'}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection