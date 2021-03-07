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
            <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" width="10%">Bild</th>
                        <th scope="col">Rolle</th>
                        <th scope="col">Leiter</th>
                        <th scope="col">Klassifizierung</th>
                        <th scope="col">Lager</th>
                        <th scope="col">Passwortänderung</th>
                    </tr>
                </thead>
            </table>
            <div class="row">
                <div class="col-lg-4">
                    <a href="{{route('users.create')}}" class="btn btn-info" role="button">Person hinzufügen</a>
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
    </script>
@endsection