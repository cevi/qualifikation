@extends('layouts.admin')
@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help_article"/>
        <div class="grid grid-cols-12 sm:gap-x-8">

            <div class="min-w-0 col-span-12 sm:col-span-6">

                {!! Form::model($help, ['method' => 'PATCH', 'action'=>['AdminHelpController@update' , $help]]) !!}
                <div class="mb-2.5">
                    {!! Form::label('title', 'Titel:', ['class' => 'field-label']) !!}
                    {!! Form::text('title', null, ['class' => 'field-input']) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::label('content', 'Inhalt:', ['class' => 'field-label']) !!}
                    {!! Form::textarea('content', null, ['class' => 'field-input','required' ,'rows' => 10]) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::submit('Hilfe-Artikel aktualisieren', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}

                {!! Form::open(['method' => 'DELETE', 'action'=>['AdminHelpController@destroy',$help]]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Lösche Hilfe-Artikel', ['class' => 'button-danger'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
        <div class="grid grid-cols-12 sm:gap-x-8">
            @include('includes.form_error')
        </div>
    </div>
@endsection
