@extends('layouts.layout')
@section('survey_content')
    <div>
        <x-layouts.page-title :title="$title" :help="$help" :header=false>
            <x-slot:actions>
                <button type="button" id="new-feedback-btn" class="button-small-primary">
                    <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 2v10M2 7h10"/></svg>
                    Neue Rückmeldung
                </button>
            </x-slot:actions>
        </x-layouts.page-title>

        <div class="mt-4">
            {{-- Karten wie auf Quali und Vergleich; eine leere Liste erscheint gar nicht --}}
            <x-posts.post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'" :card="true"/>
            <x-posts.post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'" :card="true"/>
            @if (count($posts_no_user) === 0 && count($posts_user) === 0)
                <section class="mb-4 rounded-lg border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-600 md:px-5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Noch keine Rückmeldungen. Mit «Neue Rückmeldung» hältst du fest, was dir bei einer TN-Person auffällt.
                </section>
            @endif
        </div>
    </div>
    @include('home.feedback_modal', [
        'route' => route('posts.store'),
        'campusers' => $campusers_select,
        'chooseFromUser' => true,
        'questions' => $questions,
    ])
@endsection

@push('scripts')
    @include('home.standard-texts-modal')
@endpush
