@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC9auFe3/SE0yG4aJCOd/qxew74NN7eyiSKjr7xJJMu1Jy2wf7FXITpWS1E/RY8yzuXN7VA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7ipEW6B6k+Aq20PV31bjODg4AA52W+tYtAE0jE0kMx49bjJ3FgvS56wzmyfMUHbQ4Km2b7l9+Y/+Eg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                @include('includes.form_error')
                {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUsersController@update', $user->id],  'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('avatar', 'Bild:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::file('avatar', ['class' => 'photo block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-hidden dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                </div>
                <div class="form-group">
                    {!! Form::hidden('cropped_photo_id', null, ['class' => 'form-control', 'id' => 'cropped_photo_id']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('username', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('username', null, ['class' =>'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'placeholder' => 'name@abt']) !!}
                </div>
                <div class="form-group">
                        {!! Form::label('email', 'E-Mail:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('email', null, ['class' =>'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'placeholder' => 'name@abt.ch']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('role_id', 'Rolle:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' =>'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('group_id', 'Abteilung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('group_id', [''=>'Wähle Abteilung'] + $groups, null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('leader_id', 'Gruppenleiter:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('leader_id', [''=>'Wähle Gruppenleiter'] + $leaders, null,  ['class' =>'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Änderungen speichern', ['class' =>'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}
                {!! Form::model($user, ['method' => 'DELETE', 'action'=>['AdminUsersController@destroy',$user], 'id'=> "DeleteForm"]) !!}
                <div class="form-group">
                    {!! Form::submit('Person löschen', ['class' => 'confirm focus:outline-hidden text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'])!!}
                </div>
                {!! Form::close()!!}

            </div>
        </div>
    </div>
    <div id="modal" tabindex="-1" aria-hidden="true" role="dialog" aria-labelledby="modalLabel" class="modal fade"> {{--hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">--}}
        <div class="modal-dialog modal-lg relative p-4 w-full max-w-2xl max-h-full" role="document">
            <div class="modal-content relative bg-white rounded-lg shadow-2xs dark:bg-gray-700">
                <div class="modal-header flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Vorschaubild zuschneiden</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" id="close">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
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
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="button" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-hidden focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" id="close_btn">Abbrechen</button>
                    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800" id="crop">Zuschneiden</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    @include('admin/users/photo_cropped_js')
        <script type="module">
        $(document).ready(function(){
            $('.confirm').on('click', function(e){
                e.preventDefault(); //cancel default action

                Swal.fire({
                    title: 'Person löschen?',
                    text: 'Sicher, dass die Person gelöscht werden soll?',
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
