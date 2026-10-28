@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                {!! Form::open(['action'=>'AdminGroupsController@store']) !!}
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
                        {!! Form::text('api_token', null,  ['class' => 'field-input']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Gruppe erstellen', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($groups)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">DB-ID</th>
                                <th scope="col">API-Token</th>
                                <th scope="col">Erstellt am</th>
                                <th scope="col">Geändert am</th>
                            </tr>
                        </thead>
                        @foreach ($groups as $group)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('groups.edit',$group->id)}}">{{$group->name}}</a></td>
                                    <td>{{$group['foreign_id']}}</td>
                                    <td>{{$group['api_token'] ? 'Ja' : 'Nein'}}</td>
                                    <td>{{$group->created_at ? $group->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$group->updated_at ? $group->updated_at->diffForHumans() : 'no date'}}</td>
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
