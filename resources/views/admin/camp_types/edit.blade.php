@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($camp_type, ['method' => 'Patch', 'action'=>['CampTypesController@update',$camp_type]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Änderungen speichern', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}
                @if($camp_type->camps->count()==0)
                    {!! Form::model($camp_type, ['method' => 'DELETE', 'action'=>['CampTypesController@destroy',$camp_type], 'id'=> "DeleteForm"]) !!}
                        <div class="mb-2.5">
                            {!! Form::submit('Kurs-Typ löschen', ['class' => 'button-danger'])!!}
                        </div>
                    {!! Form::close()!!}
                @endif
                </div>
        </div>
    </div>
@endsection