@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        @if (Session::has('deleted_user'))
            <p class="bg-danger">{{session('deleted_user')}}</p>
        @endif
        <div class="row">
            <div class="col-lg-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
            @if (config('app.import_db'))
                <div class="col-lg-4">
                    <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button"
                            title="{{$has_api_token ? '' : 'Deine Region hat den DB-Import nicht freigeschalten.' }}" {{$has_api_token ? '' : 'disabled'}}>
                        Personen aus Cevi-DB importieren
                    </button>
                </div>
            @endif
            <div class="col-lg-4">
                {!! Html::link('files/vorlage.xlsx', 'Vorlage herunterladen', ['class' => 'font-medium text-blue-600 dark:text-blue-500 hover:underline']) !!}
                {!! Form::open(['action' => 'AdminUsersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    {!! Form::file('csv_file', ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                </div>
                {{ Form::submit('Teilnehmerliste hochladen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800']) }}
                {!! Form::close() !!}
            </div>

        </div>
        <br>
        <table class="table table-striped table-bordered" style="width:100%" id="datatable">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col" width="10%">Bild</th>
                <th scope="col">E-Mail</th>
                <th scope="col">Rolle</th>
                <th scope="col">Leiter</th>
                <th scope="col">Klassifizierung</th>
                <th scope="col">Kurs</th>
                <th scope="col">Passwortänderung</th>
                <th scope="col">Letztes Login</th>
            </tr>
            </thead>
        </table>
        <div class="row">
            <div class="col-lg-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="importModal" aria-hidden="true">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modelHeading">Personen aus Cevi-DB importieren</h3>
                </div>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="importModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fenster schliessen</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Vorraussetzungen für DB-Import:</p>
                <ul>
                    <li>Zugewiesene Gruppe für den Kurs.</li>
                    <li>Zugewiesene Kurs ID für den Kurs.</li>
                </ul>
                <p>Erstellte Personen können sich über die Cevi-DB anmelden.</p>
                <form id="modal-form" method="POST" action="javascript:void(0)">
                    <div class="form-group">
                        <button data-remote='{{route('users.import')}}' id="importUsers"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa fa-spinner fa-spin display-none"
                                                                    id="loading-spinner"></i> Personen importieren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-gray-100 rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Personen aus Cevi-DB importieren
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Fenster schliessen</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        Vorraussetzungen für DB-Import:
                    </p>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        <ul>
                            <li>Zugewiesene Gruppe für den Kurs.</li>
                            <li>Zugewiesene Kurs-ID für den Kurs.</li>
                        </ul>
                    </p>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">Erstellte Personen können sich über die Cevi-DB anmelden.</p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <form id="modal-form" method="POST" action="javascript:void(0)">
                        <div class="form-group">
                            <button data-remote='{{route('users.import')}}' data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"><i class="fa fa-spinner fa-spin display-none"
                                                                        id="loading-spinner"></i> Personen importieren
                            </button>
                        </div>
                    </form>
                </div>
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
                ajax: "{!! route('users.CreateDataTables') !!}",
                order: [[3, "asc"], [4, "asc"], [0, "asc"]],
                columns: [
                    {data: 'user', name: 'user'},
                    {data: 'picture', name: 'picture', orderable: false, serachable: false},
                    {data: 'email', name: 'email'},
                    {
                        data: {
                            _: 'role.display',
                            sort: 'role.sort'
                        },
                        name: 'role',
                    },
                    {data: 'leader', name: 'leader'},
                    {data: 'classification', name: 'classification'},
                    {data: 'camp', name: 'camp'},
                    {data: 'password_changed', name: 'password_changed'},
                    {data: 'last_login_at', name: 'last_login_at'},

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
                beforeSend: function () {
                    $('#loading-spinner').removeClass('display-none')
                },
                complete: function () {
                    $('#loading-spinner').addClass('display-none')
                },
                success: function (res) {
                    $('#modal-form').trigger('reset');
                    $('#importModal').modal('hide');
                    location.reload();
                },
                error: function (xhr, errorType, exception) {
                    alert(exception + ': ' + xhr.responseJSON.message);
                }
            });
        });
    </script>
@endpush
