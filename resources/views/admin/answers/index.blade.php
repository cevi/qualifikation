@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                {!! Form::open(['action'=>'AdminAnswersController@store']) !!}
                    <div class="mb-2.5">
                        {!! Form::label('name', 'Antwort:', ['class' => 'field-label']) !!}
                        {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('count', 'Wertung:', ['class' => 'field-label']) !!}
                        {!! Form::text('count', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::label('description', 'Beschreibung:', ['class' => 'field-label']) !!}
                        {!! Form::text('description', null, ['class' => 'field-input', 'required']) !!}
                    </div>
                    <div class="mb-2.5">
                        {!! Form::submit('Antwort erstellen', ['class' => 'button-primary'])!!}
                    </div>
                {!! Form::close()!!}
            </div>    
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($answers)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Wertung</th>
                                <th scope="col">Name</th>
                                <th scope="col">Beschreibung</th>
                                <th scope="col">Created Date</th>
                                <th scope="col">Updated Date</th>
                            </tr>
                        </thead>
                    @foreach ($answers as $answer)
                        <tbody>
                            <tr>
                                <td>{{$answer->count}}</a></td>
                                <td><a href="{{route('answers.edit',$answer->id)}}">{{$answer->name}}</a></td>
                                <td>{{$answer->description}}</a></td>
                                <td>{{$answer->created_at ? $answer->created_at->diffForHumans() : 'no date'}}</td>
                                <td>{{$answer->updated_at ? $answer->updated_at->diffForHumans() : 'no date'}}</td>
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