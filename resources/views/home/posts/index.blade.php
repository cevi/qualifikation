@extends('layouts.layout')
@section('survey_content')
    <div>
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 md:col-span-12">
                <button type="button" id="new-feedback-btn" class="button-primary mb-3">
                    Neue Rückmeldung
                </button>
                <x-posts.post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'"/>
                <br>
                <x-posts.post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'"/>
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
