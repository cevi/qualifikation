@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-4">
                <x-forms.form :action="action('AdminQuestionsController@store')">
                <div class="mb-2.5">
                    <x-forms.select name="chapter_id" label="Kapitel:" :collection="$chapters" placeholder="Wähle Kapitel" required/>
                </div>

                <div class="mb-2.5">
                    <x-forms.text name="number" label="Nummer:" required/>
                </div>

                <div class="mb-2.5">
                    <x-forms.text name="name" label="Name:" required/>
                </div>

                <div class="mb-2.5">
                    <x-forms.text name="competence" label="Kompetenz:" required/>
                </div>

                <div class="mb-2.5">
                    <x-forms.text name="sort-index" label="Index zur Anzeige im Radar (Im Uhrzeigensinn ab 12Uhr):" required/>
                </div>

                <div class="mb-2.5">
                    <x-forms.button type="submit">Kompetenz erstellen</x-forms.button>
                </div>
                </x-forms.form>
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
