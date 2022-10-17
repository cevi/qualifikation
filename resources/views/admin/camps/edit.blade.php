@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/camps">Kurs</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Kurs</h1>
            </header>
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
                        @if (config('app.import_db'))
                            <div class="form-group">
                                {!! Form::label('group_id', 'Organisierende Gruppe:') !!}
                                {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null,  ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('foreign_id', 'Kurs ID (Cevi-DB):') !!}
                                {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                            </div>
                        @endif
                        <div class="form-group">
                            {!! Form::submit('Update Kurs', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                    @if (!Auth::user()->demo)
                        {!! Form::model($camp, ['method' => 'DELETE', 'action'=>['AdminCampsController@destroy',$camp->id], 'id'=> "DeleteForm"]) !!}
                        <div class="form-group">
                            {!! Form::submit('Kurs löschen', ['class' => 'btn btn-danger confirm'])!!}
                        </div>
                        {!! Form::close()!!}
                    @endif
                 </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
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
@endsection
