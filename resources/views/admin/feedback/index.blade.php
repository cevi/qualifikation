@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                @if (session()->has('success'))
                    <div class="alert alert-dismissable alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>
                            {!! session()->get('success') !!}
                        </strong>
                    </div>
                @endif
                {!! Form::open(['action'=>'FeedbackController@send']) !!}
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:') !!}
                        {!! Form::text('title', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('bug', 'Bug:') !!}
                        {!! Form::checkbox('bug', '1', false) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Issue auf Github erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-9">
                @if ($feedbacks)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Feedback</th>
                                <th scope="col">Von</th>
                            </tr>
                        </thead>
                        @foreach ($feedbacks as $feedback)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('feedback.edit',$feedback)}}">{{$feedback->feedback}}</a></td>
                                    <td>{{$feedback->user['username']}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
