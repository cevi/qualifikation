@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($camp, ['method' => 'Patch', 'action'=>['AdminCampsController@update',$camp->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('name', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', 'Kursleiter:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('camp_type_id', 'Kurstyp:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('status_control', '1', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                        {!! Form::label('status_control', 'Quali-Ablauf kontrollieren', [ 'class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300']) !!}
                    </div>
                <div class="form-group">
                    {!! Form::label('end_date', 'Schlussdatum:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::date('end_date', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('group_id', 'Organisierende Gruppe:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                @if (config('app.import_db'))
                        <div class="form-group">
                            {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('foreign_id', null,  ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::submit('Änderungen speichern', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                {!! Form::close()!!}
                <a href="{{ route('admin.camps.destroy', $camp) }}" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" data-confirm-delete="true">Kurs Löschen</a>

                </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
