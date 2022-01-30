@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/classification">Antworten</a></li>
            <li class="breadcrumb-item active">Klassifizierungen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Klassifizierungen</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($classification, ['method' => 'Patch', 'action'=>['AdminClassificationController@update',$classification->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Antwort:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Klassifizierung', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                 </div>
            </div>
        </div>    
    </section>   
@endsection