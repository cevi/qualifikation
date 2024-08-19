@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
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
@endsection
