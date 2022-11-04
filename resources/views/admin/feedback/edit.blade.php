@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/feedback">Feedback</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Feedback</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::model($feedback, ['method' => 'Patch', 'action'=>['FeedbackController@update',$feedback]]) !!}
                        <div class="form-group">
                            {!! Form::label('feedback', 'Feedback:') !!}
                            {!! Form::textarea('feedback', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Änderungen speichern', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                 </div>
            </div>
        </div>
    </section>
@endsection
