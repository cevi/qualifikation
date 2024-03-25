@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help_article"/>
        <div class="row">

            <div class="col-sm-6">

                {!! Form::model($help, ['method' => 'PATCH', 'action'=>['AdminHelpController@update' , $help]]) !!}
                <div class="form-group">
                    {!! Form::label('title', 'Titel:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('content', 'Inhalt:') !!}
                    {!! Form::textarea('content', null, ['class' => 'form-control','required' ,'rows' => 10]) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Hilfe-Artikel aktualisieren', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}

                {!! Form::open(['method' => 'DELETE', 'action'=>['AdminHelpController@destroy',$help]]) !!}
                <div class="form-group">
                    {!! Form::submit('Lösche Hilfe-Artikel', ['class' => 'btn btn-danger'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
        <div class="row">
            @include('includes.form_error')
        </div>
    </div>
@endsection
