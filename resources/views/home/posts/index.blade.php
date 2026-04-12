@extends('layouts.layout')
@section('survey_content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-layouts.page-title :title="$title" :help="$help" :header=false/>
        <div class="row">
            <div class="col-md-4">
                <h5 class="text-xl font-bold dark:text-white" data-modal-target="default-modal">Rückmeldung erstellen:</h5>
                <x-posts.form :post="$post_new" :campusers="$campusers_select" :chooseFromUser="true" :route="route('posts.store')"/>
            </div>
            <div class="col-md-8">
                <x-posts.post :posts="$posts_no_user" :showLeader="false" :title="'Nicht zugeordnete Rückmeldungen'"/>
                <br>
                <x-posts.post :posts="$posts_user" :showLeader="false" :title="'Zugeordnete Rückmeldungen'"/>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('home.post_delete')
@endpush
