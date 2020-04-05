@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin/">Dashboard</a></li>
            <li class="breadcrumb-item active">Lager</li>
          </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Lager</h1>
            </header>
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::open(['action'=>'AdminCampsController@store']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('year', 'Jahr:') !!}
                            {!! Form::text('year', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Lager erstellen', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>    
                <div class="col-sm-9">
                    @if ($camps)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Jahr</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Lagerleiter</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Updated Date</th>
                                </tr>
                            </thead>
                        @foreach ($camps as $camp)
                            <tbody>
                                <tr>
                                    <td>{{$camp->year}}</a></td>
                                    <td><a href="{{route('camps.edit',$camp->id)}}">{{$camp->name}}</a></td>
                                    <td>{{$camp->user['username']}}</a></td>
                                    <td>{{$camp->camp_status['name']}}</a></td>
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