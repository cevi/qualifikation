@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        <div class="row">
            <div class="col-md-12">
                <button type="button" id="new-feedback-btn" class="btn btn-primary mb-3">
                    Neue Rückmeldung
                </button>
                <x-posts.post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'"/>
                <br>
                <x-posts.post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'"/>
            </div>
        </div>
    </div>

    @include('home.feedback_modal', [
        'route' => route('posts.store'),
        'campusers' => $campusers_select,
        'chooseFromUser' => true,
    ])
@endsection

@push('scripts')
    @include('home.post_delete')
@endpush
