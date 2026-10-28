@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($feedback, ['method' => 'Patch', 'action'=>['FeedbackController@update',$feedback]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('feedback', 'Feedback:', ['class' => 'field-label']) !!}
                        {!! Form::textarea('feedback', null, ['class' => 'field-input', 'required', 'rows' => 3]) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Änderungen speichern', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}

                </div>
        </div>
    </div>
@endsection
