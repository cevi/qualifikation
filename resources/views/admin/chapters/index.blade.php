@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                <x-forms.form :action="action('AdminChaptersController@store')">
                    <div class="mb-2.5">
                        <x-forms.text name="number" label="Nummer:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Kapitel:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Kapitel erstellen</x-forms.button>
                    </div>
                </x-forms.form>
                @include('includes.form_error')
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($chapters)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Nummer</th>
                                <th scope="col">Name</th>
                                <th scope="col">Kurs-Typ</th>
                            </tr>
                        </thead>
                        @foreach ($chapters as $chapter)
                            <tbody>
                                <tr>
                                    <td>{{$chapter->number}}</a></td>
                                    <td><a href="{{route('chapters.edit',$chapter->id)}}">{{$chapter->name}}</a></td>
                                    <td>{{$chapter->camp_type ? $chapter->camp_type['name'] : 'Default'}}</td>
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
