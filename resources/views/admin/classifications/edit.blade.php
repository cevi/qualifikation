@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($classification, ['method' => 'Patch', 'action'=>['AdminClassificationController@update',$classification->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Antwort:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Update Klassifizierung', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>    
@endsection