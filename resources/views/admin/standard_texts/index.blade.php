@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-4">
                <p>Standard-Text Erfassen:</p>
                <x-forms.form :action="route('admin.standard_texts.store')" accept-charset="UTF-8" method="POST">
                    <x-forms.container> 
                        <x-forms.text label="Titel:" name="title" required=true/>
                    </x-forms.container> 
                    <x-forms.container>
                        <x-forms.text-area label="Inhalt:" name="content" rows="10"/>
                    </x-forms.container>
                    <x-forms.container>
                        <x-forms.button type="submit" class="btn btn-primary">
                            Standard-Text Erfassen
                        </x-forms.button>
                    </x-forms.container>
                </x-forms.form>
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
                                @if (!$standard_text['global'] || Auth::user()->isAdmin())
                                    <td><a href="{{route('admin.standard_texts.edit',$standard_text)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$standard_text->title}}</a></td>
                                @else
                                    <td>{{$standard_text->title}}</td>
                                @endif
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
