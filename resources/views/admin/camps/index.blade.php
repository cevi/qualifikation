@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            @if (!$camps)
                <div class="col-sm-3">
                    {!! Form::open(['action'=>'AdminCampsController@store']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('name', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('camp_type_id', 'Kurstyp:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('status_control', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-xs focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                            {!! Form::label('status_control', 'Quali-Ablauf kontrollieren', [ 'class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('end_date', 'Schlussdatum:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::date('end_date', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('group_id', 'Organisierende Gruppe:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                        </div>
                        @if (config('app.import_db'))
                            <div class="form-group">
                                {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                                {!! Form::text('foreign_id', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                            </div>
                        @endif
                        <div class="form-group">
                            {!! Form::submit('Kurs erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>
            @endif
            <div class="col-sm-9">
                @if ($camps)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Kursleiter</th>
                                <th scope="col">Kurstyp</th>
                                <th scope="col">Gruppe</th>
                                <th scope="col">End-Datum</th>
                                <th scope="col">Abgeschlossen</th>
                                @if(Auth::user()->isAdmin())
                                    <th scope="col"># Qualifikationen</th>
                                @endif
                                <th>Abschliessen?</th>
                            </tr>
                        </thead>
                    @foreach ($camps as $camp)
                        <tbody>
                            <tr>
                                <td><a href="{{route('admin.camps.edit',$camp->id)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$camp->name}}</a></td>
                                <td>{{$camp->user ? $camp->user['username'] : ''}}</a></td>
                                <td>{{$camp->camp_type ? $camp->camp_type['name'] : ''}}</a></td>
                                <td>{{$camp->group ? $camp->group['name'] : ''}}</a></td>
                                <td>{{$camp->end_date > 1 ? date('d.m.Y', strtotime($camp->end_date)) : ''}}</a></td>
                                <td>{{$camp->finish ? 'Ja' : 'Nein'}}</td>
                                @if(Auth::user()->isAdmin())
                                    <td>{{$camp->counter ?: 0}}</td>
                                @endif
                                <td>
                                    @if (!$camp->finish && Auth::user()->camp['id'] === $camp['id'])
                                        <a href="{{route('admin.camps.export')}}" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Export der Rückmeldungen</a>
                                        <br>
                                        <br>
                                        <a href="{{route('surveys.downloadPDF')}}" target="_blank" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Druckversion aller Qualifikationen</a>
                                        <br>
                                        <br>
                                        {!! Form::model($camp, ['method' => 'DELETE', 'action'=>['AdminCampsController@destroy',$camp], 'id'=> "DeleteForm"]) !!}
                                            <div class="form-group">
                                                {!! Form::submit('Kurs löschen', ['class' => 'confirm focus:outline-hidden text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'])!!}
                                            </div>
                                        {!! Form::close()!!}
                                     @endif
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>

                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
        <script type="module">
        $(document).ready(function(){
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