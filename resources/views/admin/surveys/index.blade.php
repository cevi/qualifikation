@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Qualifikationen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Qualifikationen</h1>
            </header>
            <div class="row" style="margin-bottom: 10px;">
                <a href="javascript:;" class="btn btn-info create" role="button">Qualifikationen erstellen</a>
            </div>
            <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                <thead>
                    <tr>
                        <th scope="col">Teilnehmer</th>
                        <th scope="col">Leiter</th>
                        <th scope="col">Kurs</th>
                        <th scope="col">Status</th>
                        <th scope="col">TN Qualifikation</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.create').on('click', function(e){
                e.preventDefault(); //cancel default action

                swal({
                    title: 'Qualifikationen erstellen?',
                    text: 'Dies erstellt den Qualifikationsprozess für alle Teilnehmer deines Kurses, welche ihn noch nicht haben.',
                    icon: 'info',
                    buttons: ["Abbrechen", "Ja"],
                }).then((willCreate) => {
                    if (willCreate) {
                        $.ajaxSetup({
                            headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                        });
                        $.ajax({
                            url : "{!! route('surveys.create') !!}",
                            success: function(res) {
                                location.reload();
                            }
                        });
                    }
                });
            });
            $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: "{!! route('surveys.CreateDataTables') !!}",
                columns: [
                    { data: 'user', name: 'user' },
                    { data: 'responsible', name: 'responsible' },
                    { data: 'camp', name: 'camp' },
                    { data: 'status', name: 'status', orderable:false,serachable:false},
                    { data: 'Actions', name: 'Actions', orderable:false,serachable:false},
                    
                    ]
            });
        });
    </script>
@endsection