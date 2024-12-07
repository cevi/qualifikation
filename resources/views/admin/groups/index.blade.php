@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['action'=>'AdminGroupsController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('foreign_id', 'Gruppen ID (Cevi-DB):') !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('url', 'URL (Cevi-DB):') !!}
                        {!! Form::text('url', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('api_token', 'API Token (Cevi-DB):') !!}
                        {!! Form::text('api_token', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Gruppe erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-9">
                @if ($groups)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">DB-ID</th>
                                <th scope="col">API-Token</th>
                                <th scope="col">Erstellt am</th>
                                <th scope="col">Geändert am</th>
                            </tr>
                        </thead>
                        @foreach ($groups as $group)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('groups.edit',$group->id)}}">{{$group->name}}</a></td>
                                    <td>{{$group['foreign_id']}}</td>
                                    <td>{{$group['api_token'] ? 'Ja' : 'Nein'}}</td>
                                    <td>{{$group->created_at ? $group->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$group->updated_at ? $group->updated_at->diffForHumans() : 'no date'}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
