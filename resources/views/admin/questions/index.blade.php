@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-4">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminQuestionsController@store']) !!}
                <div class="mb-2.5">
                    {!! Form::label('chapter_id', 'Kapitel:', ['class' => 'field-label']) !!}
                    {!! Form::select('chapter_id', [''=>'Wähle Kapitel'] + $chapters, null,  ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('number', 'Nummer:', ['class' => 'field-label']) !!}
                    {!! Form::text('number', null, ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('name', 'Name:', ['class' => 'field-label']) !!}
                    {!! Form::text('name', null, ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('competence', 'Kompetenz:', ['class' => 'field-label']) !!}
                    {!! Form::text('competence', null, ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::label('sort-index', 'Index zur Anzeige im Radar (Im Uhrzeigensinn ab 12Uhr):', ['class' => 'field-label']) !!}
                    {!! Form::text('sort-index', null, ['class' => 'field-input', 'required']) !!}
                </div>

                <div class="mb-2.5">
                    {!! Form::submit('Kompetenz erstellen', ['class' => 'button-primary'])!!}
                </div>
                {!! Form::close()!!}
                @include('includes.form_error')
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-8">
                @if ($questions)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Kapitel-Nr.</th>
                                <th scope="col">Kapitel</th>
                                <th scope="col">Nummer</th>
                                <th scope="col">Kompetenz</th>
                                <th scope="col">Beschreibung</th>
                                <th scope="col">Sort-Index</th>
                            </tr>
                        </thead>
                    @foreach ($questions as $question)
                        <tbody>
                            <tr>
                                <td>{{$question->chapter['number']}}</td>
                                <td>{{$question->chapter['name']}}</td>
                                <td>{{$question->number}}</td>
                                <td><a href="{{route('questions.edit',$question->id)}}">{{$question->competence}}</a></td>
                                <td>{{$question->name}}</td>
                                <td>{{$question['sort-index']}}</td>
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
