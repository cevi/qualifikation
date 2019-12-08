@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin/">Dashboard</a></li>
            <li class="breadcrumb-item active">Kapitel</li>
          </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Kapitel</h1>
            </header>
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::open(['action'=>'AdminChaptersController@store']) !!}
                        <div class="form-group">
                            {!! Form::label('number', 'Nummer:') !!}
                            {!! Form::text('number', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Kapitel:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
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
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Updated Date</th>
                                </tr>
                            </thead>
                            @foreach ($chapters as $chapter)
                                <tbody>
                                    <tr>
                                        <td>{{$chapter->number}}</a></td>
                                        <td><a href="{{route('chapters.edit',$chapter->id)}}">{{$chapter->name}}</a></td>
                                        <td>{{$chapter->created_at ? $chapter->created_at->diffForHumans() : 'no date'}}</td>
                                        <td>{{$chapter->updated_at ? $chapter->updated_at->diffForHumans() : 'no date'}}</td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection