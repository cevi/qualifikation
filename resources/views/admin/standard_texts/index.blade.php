@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-4">
                <p>Standard-Text Erfassen:</p>
                {!! Form::open(['method' => 'POST', 'action'=>'StandardTextsController@store']) !!}
                <div class="form-group">
                    {!! Form::label('title', 'Titel:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::text('title', null, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('content', 'Inhalt:', ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::textarea('content', null, ['rows' => 10, 'required', 'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Standard-Text Erfassen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="col-md-8">
                @if ($standard_texts)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Titel</th>
                                <th scope="col">Beschreibung</th>
                                <th scope="col">Kurs</th>
                                <th scope="col">Global</th>
                            </tr>
                        </thead>
                    @foreach ($standard_texts as $standard_text)
                        <tbody>
                            <tr>
                                <td><a href="{{route('admin.standard_texts.edit',$standard_text)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$standard_text->title}}</a></td>
                                <td>{{$standard_text['content']}}</a></td>
                                <td>{{$standard_text->camp ? $standard_text->camp['name'] : ''}}</a></td>
                                <td>{{$standard_text['global'] ? 'Ja' : 'Nein'}}</a></td>
                            </tr>
                        </tbody>
                    @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
