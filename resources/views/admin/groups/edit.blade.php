@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                {!! Form::model($group, ['method' => 'Patch', 'action'=>['AdminGroupsController@update',$group->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('foreign_id', 'Gruppen ID (Cevi-DB):') !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('api_token', 'API Token (Cevi-DB):') !!}
                        {!! Form::password('api_token', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Update Gruppe', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}

                {!! Form::model($group, ['method' => 'DELETE', 'action'=>['AdminGroupsController@destroy',$group->id], 'id'=> "DeleteForm"]) !!}
                <div class="form-group">
                    {!! Form::submit('Gruppe löschen', ['class' => 'btn btn-danger confirm'])!!}
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
                    text: 'Beim Kurs löschen werden alle Leiter, Teilnehmer und Qualifikationen gelöscht.',
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
