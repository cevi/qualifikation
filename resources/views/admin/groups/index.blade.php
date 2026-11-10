@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                <x-forms.form :action="action('AdminGroupsController@store')">
                    <div class="mb-2.5">
                        <x-forms.text name="name" label="Name:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="foreign_id" label="Gruppen ID (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="url" label="URL (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text name="api_token" label="API Token (Cevi-DB):"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Gruppe erstellen</x-forms.button>
                    </div>
                </x-forms.form>
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
