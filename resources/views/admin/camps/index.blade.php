@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin/">Dashboard</a></li>
            <li class="breadcrumb-item active">Kurs</li>
          </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Kurs</h1>
            </header>
            <div class="row">
                @if (!$camps)
                <div class="col-sm-3">
                    {!! Form::open(['action'=>'AdminCampsController@store']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('camp_type_id', 'Kurstyp:') !!}
                            {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'form-control', 'required']) !!}
                        </div>
                        @if (config('app.import_db'))
                            <div class="form-group">
                                {!! Form::label('group_id', 'Organisierende Gruppe:') !!}
                                {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):') !!}
                                {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                            </div>
                        @endif
                        <div class="form-group">
                            {!! Form::submit('Kurs erstellen', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>    
                @endif
                <div class="col-sm-9">
                    @if ($camps)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Kursleiter</th>
                                    <th scope="col">Kurstyp</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Updated Date</th>
                                </tr>
                            </thead>
                        @foreach ($camps as $camp)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('admin.camps.edit',$camp->id)}}">{{$camp->name}}</a></td>
                                    <td>{{$camp->user['username']}}</a></td> 
                                    <td>{{$camp->camp_type['name']}}</a></td> 
                                    <td>{{$camp->created_at ? $camp->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$camp->updated_at ? $camp->updated_at->diffForHumans() : 'no date'}}</td>
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