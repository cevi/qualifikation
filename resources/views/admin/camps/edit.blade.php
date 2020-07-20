@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/camps">Lager</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Lager</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($camp, ['method' => 'Patch', 'action'=>['AdminCampsController@update',$camp->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('year', 'Jahr:') !!}
                            {!! Form::text('year', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('user_id', 'Lagerleiter:') !!}
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('camp_type_id', 'Lagertyp:') !!}
                            {!! Form::select('camp_type_id', [''=>'Wähle Lagertyp'] + $camptypes, null,  ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Lager', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                    {!! Form::model($camp, ['method' => 'DELETE', 'action'=>['AdminCampsController@destroy',$camp->id], 'id'=> "myForm"]) !!}
                    <div class="form-group">
                        {!! Form::submit('Lager löschen', ['class' => 'btn btn-danger confirm'])!!}
                    </div>
                    {!! Form::close()!!}
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
                    title: 'Lager löschen?',
                    text: 'Beim Lager löschen werden alle Leiter, Teilnehmer und Umfragen gelöscht.',
                    icon: 'warning',
                    buttons: ["Abbrechen", "Ja!"],
                }).then((willDelete) => {
                    if (willDelete) {
                        document.getElementById("myForm").submit();
                    }
                });
            });
        });
    </script>
@endsection