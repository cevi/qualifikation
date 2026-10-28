@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                @if (session()->has('success'))
                    <x-layouts.alert id="alert-success"><strong>{!! session()->get('success') !!}</strong></x-layouts.alert>
                @endif
                {!! Form::open(['action'=>'FeedbackController@send']) !!}
                    <div class="mb-2.5">
                        {!! Form::label('title', 'Titel:', ['class' => 'field-label']) !!}
                        {!! Form::text('title', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('description', 'Beschreibung:', ['class' => 'field-label']) !!}
                        {!! Form::textarea('description', null, ['class' => 'field-input', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('bug', 'Bug:', ['class' => 'field-label']) !!}
                        {!! Form::checkbox('bug', '1', false) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Issue auf Github erstellen', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($feedbacks)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
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
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
