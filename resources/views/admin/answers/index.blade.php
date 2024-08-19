@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'AdminAnswersController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Antwort:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('count', 'Wertung:') !!}
                        {!! Form::text('count', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:') !!}
                        {!! Form::text('description', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Antwort erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="col-sm-9">
                @if ($answers)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Wertung</th>
                                <th scope="col">Name</th>
                                <th scope="col">Beschreibung</th>
                                <th scope="col">Created Date</th>
                                <th scope="col">Updated Date</th>
                            </tr>
                        </thead>
                    @foreach ($answers as $answer)
                        <tbody>
                            <tr>
                                <td>{{$answer->count}}</a></td>
                                <td><a href="{{route('answers.edit',$answer->id)}}">{{$answer->name}}</a></td>
                                <td>{{$answer->description}}</a></td>
                                <td>{{$answer->created_at ? $answer->created_at->diffForHumans() : 'no date'}}</td>
                                <td>{{$answer->updated_at ? $answer->updated_at->diffForHumans() : 'no date'}}</td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>
                
                @endif

            </div>
        </div>
    </div>
@endsection