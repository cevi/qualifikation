@extends('layouts.admin')

@section('content')
    <div class="px-[15px] md:px-12">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help"/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-3">
                @if (session()->has('success'))
                    <x-layouts.alert id="alert-success"><strong>{!! session()->get('success') !!}</strong></x-layouts.alert>
                @endif
                @include('includes.form_error')
                <x-forms.form :action="action('FeedbackController@send')">
                    <div class="mb-2.5">
                        <x-forms.text name="title" label="Titel:" required/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.text-area name="description" label="Beschreibung:" required rows="3"/>
                    </div>
                    <div class="mb-2.5">
                        <label for="bug" class="field-label">Bug:</label>
                        <x-forms.checkbox name="bug"/>
                    </div>
                    <div class="mb-2.5">
                        <x-forms.button type="submit">Issue auf Github erstellen</x-forms.button>
                    </div>
                </x-forms.form>
            </div>
            <div class="min-w-0 col-span-12 sm:col-span-9">
                @if ($feedbacks)
                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th scope="col">Feedback</th>
                                <th scope="col">Von</th>
                            </tr>
                        </thead>
                        @foreach ($feedbacks as $feedback)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('feedback.edit',$feedback)}}">{{$feedback->feedback}}</a></td>
                                    <td>{{$feedback->user['username']}}</td>
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
