@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/groups">Gruppe</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Gruppe</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($group, ['method' => 'Patch', 'action'=>['AdminGroupsController@update',$group->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('foreign_id', 'Gruppen ID (Cevi-DB):') !!}
                            {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('api_token', 'API Token (Cevi-DB):') !!}
                            {!! Form::password('api_token', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Gruppe', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($group, ['method' => 'DELETE', 'action'=>['AdminGroupsController@destroy',$group->id], 'id'=> "DeleteForm"]) !!}
                    <div class="form-group">
                        {!! Form::submit('Gruppe löschen', ['class' => 'btn btn-danger confirm'])!!}
                    </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.confirm').on('click', function(e){
                e.preventDefault(); //cancel default action

                swal({
                    title: 'Kurs löschen?',
                    text: 'Beim Kurs löschen werden alle Leiter, Teilnehmer und Qualifikationen gelöscht.',
                    icon: 'warning',
                    buttons: ["Abbrechen", "Ja!"],
                }).then((willDelete) => {
                    if (willDelete) {
                        document.getElementById("DeleteForm").submit();
                    }
                });
            });
        });
    </script>
@endsection
