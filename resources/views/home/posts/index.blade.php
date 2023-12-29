@extends('layouts.layout')
@section('survey_content')
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Rückmeldungen</h1>
            </header>
            <div class="row">
                <div class="col-md-4">
                    Rückmeldung erstellen:
                    {!! Form::open(['method' => 'POST', 'action'=>'PostController@store',  'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::hidden('post_id', null, ['id' => 'post_id']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment', 'Rückmeldung:') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', 'Benutzer:') !!}
                        {!! Form::select('user_id', [''=>'Wähle Teilnehmende'] + $users_select, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            {!! Form::label('file', 'Datei:') !!}
                            {!! Form::file('file') !!}
                        </div>
                        <div class="col-md-6 form-group">
                            {!! Form::label('show_on_survey', 'Sichtbar für Qualifikation:') !!}
                            {!! Form::checkbox('show_on_survey', '1', false) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Rückmeldung Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-md-8">
                    <x-post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'"/>
                    <br>
                    <x-post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'"/>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('home.post_delete')
@endsection
