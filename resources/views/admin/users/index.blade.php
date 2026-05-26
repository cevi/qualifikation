@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        @if (Session::has('deleted_user'))
            <p class="bg-danger">{{session('deleted_user')}}</p>
        @endif
        <div class="row">
            <div class="col-lg-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
            @if (config('app.import_db'))
                <div class="col-lg-4">
                    <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button" id="showImport"
                            title="{{$has_api_token ? '' : 'Deine Region hat den DB-Import nicht freigeschalten.' }}" {{$has_api_token ? '' : 'disabled'}}>
                        Personen aus Cevi-DB importieren
                    </button>
                </div>
            @endif
            <div class="col-lg-4">
                {!! Html::link('files/vorlage.xlsx', 'Vorlage herunterladen', ['class' => 'font-medium text-blue-600 dark:text-blue-500 hover:underline']) !!}
                {!! Form::open(['action' => 'AdminUsersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group">
                    {!! Form::file('csv_file', ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-hidden dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                </div>
                {{ Form::submit('Teilnehmerliste hochladen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800']) }}
                {!! Form::close() !!}
            </div>

        </div>
        <br>
        <table class="table table-striped table-bordered" style="width:100%" id="datatable">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Bild</th>
                <th scope="col">E-Mail</th>
                <th scope="col">Rolle</th>
                <th scope="col">Leiter</th>
                <th scope="col">Klassifizierung</th>
                <th scope="col">Kurs</th>
                <th scope="col">Abteilung</th>
                <th scope="col">Passwortänderung</th>
                <th scope="col">Letztes Login</th>
            </tr>
            </thead>
        </table>
        <div class="row">
            <div class="col-lg-4">
                <a href="{{route('admin.users.create')}}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Person manuell
                    erstellen oder zuordnen</a>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="module">
        $(document).ready(function () {
            var table = $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                buttons: [],
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: "{!! route('users.CreateDataTables') !!}",
                order: @json($initialOrder),
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
                    {data: 'group', name: 'group'},
                    {data: 'password_changed', name: 'password_changed'},
                    {data: 'last_login_at', name: 'last_login_at'},

                ]
            });

            var defaultOrder = @json($defaultOrder);
            var isPopState = false;
            var isInitial = true;

            table.on('order.dt', function () {
                if (isInitial) {
                    isInitial = false;
                    return;
                }
                if (isPopState) {
                    isPopState = false;
                    return;
                }
                var order = table.order();
                var orderStr = order.map(o => `${o[0]}:${o[1]}`).join(',');
                var url = new URL(window.location.href);
                var currentOrder = url.searchParams.get('order');
                if (currentOrder !== orderStr) {
                    url.searchParams.set('order', orderStr);
                    window.history.pushState({ order: order }, '', url.toString());
                }
            });

            $(window).on('popstate', function () {
                var orderParam = new URLSearchParams(window.location.search).get('order');
                var newOrder = defaultOrder;
                if (orderParam) {
                    var parsed = [];
                    var isValid = true;
                    var items = orderParam.split(',');
                    for (var i = 0; i < items.length; i++) {
                        var parts = items[i].split(':');
                        if (parts.length === 2) {
                            var colIndex = parseInt(parts[0], 10);
                            var dir = parts[1];
                            if (!isNaN(colIndex) && colIndex >= 0 && colIndex <= 9 && (dir === 'asc' || dir === 'desc')) {
                                parsed.push([colIndex, dir]);
                            } else {
                                isValid = false;
                                break;
                            }
                        } else {
                            isValid = false;
                            break;
                        }
                    }
                    if (isValid && parsed.length > 0) {
                        newOrder = parsed;
                    } else {
                        console.warn('Malformed or invalid order URL parameter:', orderParam);
                    }
                }
                isPopState = true;
                table.order(newOrder).draw();
            });

            $('#showImport').on('click', function () {
                Swal.fire({
                    title: 'Personen aus Cevi-DB importieren',
                    html: `<div class="p-4 md:p-5 space-y-4">
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
                </div>`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Importieren',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: 'blue',
                    cancelButtonColor: 'red',
                    showLoaderOnConfirm: true,
                    preConfirm: async (login) => {
                        try {
                            const url=  "{!! route('users.import') !!}";
                            const response = await fetch(url,{
                                method: "POST",
                                headers: { 'Content-Type': 'application/json',  
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            });
                            
                            if (!response.ok) {
                                const responseJSON =  await response.json();
                                return Swal.showValidationMessage(responseJSON.error);
                            }
                            return response.json();
                        }
                        catch (error) {
                            Swal.showValidationMessage(`
                                Request failed: ${error}
                            `);
                        }

                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    location.reload();
                });
            });
        });
    </script>
@endpush
