@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'AdminChaptersController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('number', 'Nummer:') !!}
                        {!! Form::text('number', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('name', 'Kapitel:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Kapitel erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
                @include('includes.form_error')
            </div>
            <div class="col-sm-9">
                @if ($chapters)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nummer</th>
                                <th scope="col">Name</th>
                                <th scope="col">Kurs-Typ</th>
                            </tr>
                        </thead>
                        @foreach ($chapters as $chapter)
                            <tbody>
                                <tr>
                                    <td>{{$chapter->number}}</a></td>
                                    <td><a href="{{route('chapters.edit',$chapter->id)}}">{{$chapter->name}}</a></td>
                                    <td>{{$chapter->camp_type ? $chapter->camp_type['name'] : 'Default'}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
