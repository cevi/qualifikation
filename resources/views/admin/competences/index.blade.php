@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                <x-forms.form :action="action('AdminCompetencesController@store')">
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Beschreibung:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.select name="question_id" label="Kompetenz:" :collection="$questions" placeholder="Wähle Kompetenz" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.select name="camp_type_id" label="Kurs-Typ:" :collection="$camp_types" placeholder="Wähle Kurs-Typ" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Kernkompetenzen erstellen</x-forms.button>
                    </div>
                </x-forms.form>
                @include('includes.form_error')
            </div>    
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($competences)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Kompetenz</th>
                                <th scope="col">Kurs-Typ</th>
                                <th scope="col">Erstellt am</th>
                                <th scope="col">Geändert am</th>
                            </tr>
                        </thead>
                        @foreach ($competences as $competence)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('competences.edit',$competence->id)}}">{{$competence->name}}</a></td>
                                    <td>{{$competence->question['name']}}</td>
                                    <td>{{$competence->camp_type['name']}}</td>
                                    <td>{{$competence->created_at ? $competence->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$competence->updated_at ? $competence->updated_at->diffForHumans() : 'no date'}}</td>
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