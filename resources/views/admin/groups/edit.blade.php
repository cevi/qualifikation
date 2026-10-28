@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-6">
                {!! Form::model($group, ['method' => 'Patch', 'action'=>['AdminGroupsController@update',$group->id]]) !!}
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Name:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('foreign_id', 'Gruppen ID (Cevi-DB):', ['class' => 'field-label']) !!}
                        {!! Form::text('foreign_id', null,  ['class' => 'field-input']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('url', 'URL (Cevi-DB):', ['class' => 'field-label']) !!}
                        {!! Form::text('url', null,  ['class' => 'field-input']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('api_token', 'API Token (Cevi-DB):', ['class' => 'field-label']) !!}
                        {!! Form::password('api_token', ['class' => 'field-input']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Update Gruppe', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}

                @error('group')
                    <x-layouts.alert type="danger">{{ $message }}</x-layouts.alert>
                @enderror
                {!! Form::model($group, ['method' => 'DELETE', 'action'=>['AdminGroupsController@destroy',$group->id], 'id'=> "DeleteForm"]) !!}
                <div class="mb-2.5">
                    {!! Form::submit('Gruppe löschen', ['class' => 'button-danger confirm', 'data-confirm' => 'Gruppe löschen?', 'data-confirm-text' => 'Willst du die Gruppe wirklich löschen?'])!!}
                </div>
                {!! Form::close()!!}
                </div>
        </div>
    </div>
@endsection
