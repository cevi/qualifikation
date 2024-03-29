@extends('layouts.admin')
@section('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">Personen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Person</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUsersController@update', $user->id],  'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('avatar', 'Bild:') !!}
                        {!! Form::file('avatar', ['class' => 'photo']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::hidden('cropped_photo_id', null, ['class' => 'form-control', 'id' => 'cropped_photo_id']) !!}
                    </div>
                    <div class="form-group">
                            {!! Form::label('username', 'Name:') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'name@abt']) !!}
                    </div>
                    <div class="form-group">
                            {!! Form::label('email', 'E-Mail:') !!}
                            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'name@abt.ch']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('role_id', 'Rolle:') !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('leader_id', 'Gruppenleiter:') !!}
                        {!! Form::select('leader_id', [''=>'Wähle Gruppenleiter'] + $leaders, null,  ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Änderungen speichern', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    {!! Form::model($user, ['method' => 'DELETE', 'action'=>['AdminUsersController@destroy',$user->id], 'id'=> "DeleteForm"]) !!}
                    <div class="form-group">
                        {!! Form::submit('Person löschen', ['class' => 'btn btn-danger confirm'])!!}
                    </div>
                    {!! Form::close()!!}


                </div>
            </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Vorschaubild zuschneiden</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <div class="row">
                                <div class="col-md-8">
                                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                                </div>
                                <div class="col-md-4">
                                    <div class="preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-primary" id="crop">Zuschneiden</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin/users/photo_cropped_js')
    <script>
        $(document).ready(function(){
            $('.confirm').on('click', function(e){
                e.preventDefault(); //cancel default action

                swal({
                    title: 'Person löschen?',
                    text: 'Sicher, dass die Person gelöscht werden soll?',
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
