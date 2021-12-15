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
            <li class="breadcrumb-item active">Erfassen</li>
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
                    <p>Person Suchen:</p>
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@add',  'files' => true]) !!}
                    <div class="form-group">
                            {!! Form::label('username', 'Name:') !!}
                            {!! Form::text('username', null, ['class' => 'form-control autocomplete_txt', 'placeholder' => 'name@abt', 'required', 'onchange' => "Hide_Form()"]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('role_id', 'Role:') !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    {!! Form::hidden('user_id', null, ['class' => 'form-control autocomplete_txt']) !!}
                    <div class="form-group">
                        {!! Form::submit('Person Hinzufügen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-sm-6">
                    <p>Person Erstellen:</p>
                    @include('includes.form_error')
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@store',  'files' => true]) !!}
                    <div class="form-group">
                            {!! Form::label('username', 'Name:') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'name@abt', 'required']) !!}
                    </div>
                    <div id="user_information_form">
                        <div class="form-group">
                                {!! Form::label('email', 'E-Mail:') !!}
                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'name@abt.ch', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('avatar', 'Photo:') !!}
                            {!! Form::file('avatar', ['class' => 'photo']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::hidden('cropped_photo_id', null, ['class' => 'form-control', 'id' => 'cropped_photo_id']) !!}
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
                            {!! Form::label('classification_id', 'Klassifizierung:') !!}
                            {!! Form::select('classification_id', [''=>'Wähle Klassifizierung'] + $classifications, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('is_active', 'Status:') !!}
                            {!! Form::select('is_active', array(1 => "Aktiv", 0 => 'Nicht Aktiv'), null,  ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('password', 'Password:') !!}
                            {!! Form::password('password', ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Person Erstellen', ['class' => 'btn btn-primary'])!!}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script type="text/javascript">
        //autocomplete script
        $(document).on('focus','.autocomplete_txt',function(){
            type = $(this).attr('name');
        
            if(type =='username')autoType='username';  
        
            $(this).autocomplete({
                minLength: 3,
                highlight: true,
                source: function( request, response ) {
                        $.ajax({
                            url: "{{ route('searchajaxuser') }}",
                            dataType: "json",
                            data: {
                                term : request.term,
                                type : type,
                            },
                            success: function(data) {
                                var array = $.map(data, function (item) {
                                return {
                                    label: item['username'],
                                    value: item[autoType],
                                    data : item
                                }
                            });
                                response(array)
                            }
                        });
                },
                select: function( event, ui ) {
                    var data = ui.item.data;   
                    $("[name='username']").val(data.username);
                    $("[name='user_id']").val(data.id);
                }
            });
        });
    </script>
@endsection