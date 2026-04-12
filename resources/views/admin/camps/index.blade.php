@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                <x-forms.form :action="route('admin.camps.store')" accept-charset="UTF-8" method="POST">
                    <x-forms.container> 
                        <x-forms.text label="Name:" name="name" required=true/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.select label="Kurstyp:" name="camp_type_id" :collection="$camptypes" :withEmptyOption="true" required=true/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.checkbox label="Quali-Ablauf kontrollieren" name="status_control"/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.text label="Schlussdatum:" name="end_date" required=true type="date"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.select label="Organisierende Gruppe:" name="group_id" :collection="$groups" :withEmptyOption="true" required=true/>
                    </x-forms.container>
                    @if (config('app.import_db'))
                        <x-forms.container>
                            <x-forms.text label="Kurs ID (Cevi-DB)" name="foreign_id"/>
                        </x-forms.container>
                    @endif
                    <x-forms.container>
                        <x-forms.button type="submit">
                            Kurs erstellen
                        </x-forms.button>
                    </x-forms.container> 
                </x-forms.form>
            </div>
            <div class="col-sm-9">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Kursleiter</th>
                            <th scope="col">E-Mail</th>
                            <th scope="col">Kurstyp</th>
                            <th scope="col">Gruppe</th>
                            <th scope="col">End-Datum</th>
                            <th scope="col">Abgeschlossen</th>
                            <th scope="col"># Qualifikationen</th>
                            <th>Abschliessen?</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
        <script type="module">
        $(document).ready(function () {
            $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                buttons: [],
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: "{!! route('camps.CreateDataTables') !!}",
                order: [[3, "asc"], [4, "asc"], [0, "asc"]],
                columns: [
                    {data: 'camp', name: 'camp'},
                    {data: 'username', name: 'username'},
                    {data: 'email', name: 'email'},
                    {data: 'camp_type', name: 'camp_type'},
                    {data: 'group', name: 'group'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'finish', name: 'finish'},
                    {data: 'counter', name: 'counter'},
                    {data: 'actions', name: 'actions'},
                ]
            });
            $('.confirm').on('click', function(e){
                e.preventDefault(); //cancel default action
                Swal.fire({
                    title: 'Kurs abschliessen/löschen?',
                    text: "Beim Kurs abschliessen/löschen werden alle Qualifikationen und hochgeladenen Dokumente gelöscht.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ja',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: 'blue',
                    cancelButtonColor: 'red',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("DeleteForm").submit();
                    }
                });
            });
        });
    </script>
@endpush