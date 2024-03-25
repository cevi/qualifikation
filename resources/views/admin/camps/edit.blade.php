@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($camp, ['method' => 'Patch', 'action'=>['AdminCampsController@update',$camp->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', 'Kursleiter:') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('camp_type_id', 'Kurstyp:') !!}
                        {!! Form::select('camp_type_id', [''=>'Wähle Kurstyp'] + $camptypes, null,  ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status_control', 'Quali-Ablauf kontrollieren:') !!}
                        {!! Form::checkbox('status_control', '1',  $camp['status_control']) !!}
                    </div>
                <div class="form-group">
                    {!! Form::label('end_date', 'Schlussdatum:') !!}
                    {!! Form::date('end_date', null,  ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('group_id', 'Organisierende Gruppe:') !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'form-control', 'required']) !!}
                </div>
                @if (config('app.import_db'))
                        <div class="form-group">
                            {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):') !!}
                            {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::submit('Änderungen speichern', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
                {!! Form::model($camp, ['method' => 'DELETE', 'action'=>['AdminCampsController@destroy',$camp->id], 'id'=> "DeleteForm"]) !!}
                <div class="form-group">
                    {!! Form::submit('Kurs löschen', ['class' => 'btn btn-danger confirm'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        $(document).ready(function(){
            $('.confirm').on('click', function(e){
                e.preventDefault(); //cancel default action

                swal({
                    title: 'Kurs löschen?',
                    text: 'Beim Kurs löschen werden alle Qualifikationen und hochgeladenen Dokumente gelöscht.',
                    icon: 'warning',
                    buttons: ["Abbrechen", "Ja!"],
                }).then((willDelete) => {
                    if (willDelete) {
                        document.getElementById("DeleteForm").submit();
                    }
                });
            });
        });
    </script>
@endpush
