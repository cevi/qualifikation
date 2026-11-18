@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-4">
                <p>Artikel Erfassen:</p>
                <x-forms.form :action="action('AdminHelpController@store')">
                <div class="mb-2.5">
                    <x-forms.text name="title" label="Titel:"/>
                </div>
                <div class="mb-2.5">
                    <x-forms.text-area name="content" label="Inhalt:" required rows="10"/>
                </div>
                <div class="mb-2.5">
                    <x-forms.button type="submit">Artikel erfassen</x-forms.button>
                </div>
                </x-forms.form>
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
