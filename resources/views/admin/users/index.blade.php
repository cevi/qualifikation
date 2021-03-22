@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Personen</li>
            </ul>
        </div>
    </div>
    @if (Session::has('deleted_user'))
        <p class="bg-danger">{{session('deleted_user')}}</p> 
    @endif
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Personen</h1>
                {!! Html::link('files/vorlage.xlsx', 'Vorlage herunterladen') !!}
            </header>
            <div class="row">
                <div class="col-lg-4">
                    <a href="{{route('users.create')}}" class="btn btn-info" role="button">Person erstellen</a>
                </div>
                @if (config('app.import_db'))
                    <div class="col-lg-4">
                        <button id="showImport" class="btn btn-info btn-sm">Personen aus Cevi-DB importieren</button>
                    </div>
                @endif
            </div>
            <br>
            <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" width="10%">Bild</th>
                        <th scope="col">Rolle</th>
                        <th scope="col">Leiter</th>
                        <th scope="col">Klassifizierung</th>
                        <th scope="col">Kurs</th>
                        <th scope="col">Passwortänderung</th>
                    </tr>
                </thead>
            </table>
            <div class="row">
                <div class="col-lg-4">
                    <a href="{{route('users.create')}}" class="btn btn-info" role="button">Person erstellen</a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-4">
                    {!! Form::open(['action' => 'AdminUsersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                        <div class="form-group">
                            {{ Form::file('csv_file',['class' => 'dropify'])}}
                        </div>
                        {{ Form::submit('Teilnehmerliste hochladen', ['class' => 'btn btn-primary']) }}  
                    {!! Form::close() !!}
                </div>
            </div>
        </div>  
    </section>
    <div class="modal fade" id="importModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Personen aus Cevi-DB importieren</h4>
                </div>
                <div class="modal-body">
                    <p>Vorraussetzungen für DB-Import:</p>
                    <ul>
                        <li>Zugewiesene Gruppe für den Kurs.</li>
                        <li>Zugewiesene Kurs ID für den Kurs.</li>
                        <li>Bis auf den Kursleiter-Benutzer (aktiver Benutzer) wurden alle Personen über den DB-Import erstellt.</li>
                    </ul>
                    <p>Benutzername und Passwort werden nirgendwo gespeichert.</p>
                    <p>Erstellte Personen haben Passwort als Benutzernamen (unter Profil (oben rechts) änderbar).</p>
                    <form id="modal-form" method="POST" action="javascript:void(0)">
                        <div class="form-group">
                            {!! Form::label('username', 'Benutzername / Email:') !!}
                            {!! Form::text('username', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('password', 'Password:') !!}
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <button data-remote='{{route('users.import')}}' id="importUsers" class="btn btn-info btn-sm">Personen importieren</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('.dropify').dropify();
            $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: "{!! route('users.CreateDataTables') !!}",
                order: [[ 2, "asc" ], [ 0, "asc" ]],
                columns: [
                    { data: 'user', name: 'user' },
                    { data: 'picture', name: 'picture', orderable:false,serachable:false},
                    { data: 'role', name: 'role' },
                    { data: 'leader', name: 'leader' },
                    { data: 'classification', name: 'classification' },
                    { data: 'camp', name: 'camp' },
                    { data: 'password_changed', name: 'password_changed' },
                    
                    ]
            });
        });
        $('#showImport').on('click', function () { 
            $('#importModal').modal('show');
        });

        $('#importUsers').on('click', function () { 

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var url = $(this).data('remote');
            // confirm then
            $.ajax({
                url: url,
                method: 'POST',
                data:{
                name: $('#modal-form input[name="username"]').val(),
                password: $('#modal-form input[name="password"]').val()},
                success:function(res)
                {   
                    $('#modal-form').trigger('reset');
                    $('#importModal').modal('hide');
                    location.reload();
                }
            });
        });
    </script>
@endsection