@extends('layouts.admin')

@section('content')
    <div class="px-4 md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-4">
                <p>Artikel Erfassen:</p>
                {!! Form::open(['method' => 'POST', 'action'=>'AdminHelpController@store']) !!}
                <div class="mb-2.5">
                    {!! Form::label('title', 'Titel:', ['class' => 'field-label']) !!}
                    {!! Form::text('title', null, ['class' => 'field-input']) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::label('content', 'Inhalt:', ['class' => 'field-label']) !!}
                    {!! Form::textarea('content', null, ['class' => 'field-input','required' ,'rows' => 10]) !!}
                </div>
                <div class="mb-2.5">
                    {!! Form::submit('Artikel Erfassen', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="min-w-0 col-span-12 md:col-span-8">
                <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="list-table" id="datatable">
                    <thead>
                    <tr>
                        {{-- <th></th> --}}
                        <th>Titel</th>
                        <th>Inhalt</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($helps as $help)
                        <tr>
                            {{-- <th></th> --}}
                            <td><a href="{{route('helps.edit', $help)}}">{{$help['title']}}</a></td>
                            <td>{!! nl2br($help['content']) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
@endsection
