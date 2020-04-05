@extends('layouts.admin')
@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">Teilnehmer</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Teilnehmer</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@store']) !!}
                    <div class="form-group">
                            {!! Form::label('username', 'Name:') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'name@abt', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('role_id', 'Role:') !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('leader_id', 'Gruppenleiter:') !!}
                        {!! Form::select('leader_id', [''=>'Wähle Gruppenleiter'] + $leaders, null,  ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('is_active', 'Status:') !!}
                        {!! Form::select('is_active', array(1 => "Aktiv", 0 => 'Nicht Aktiv'), null,  ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('password', 'Password:') !!}
                        {!! Form::password('password', ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('User Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
</div>
@endsection