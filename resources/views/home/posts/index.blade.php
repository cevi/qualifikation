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

    <!-- Feedback Modal -->
    <div id="feedback-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Backdrop -->
        <div id="feedback-modal-backdrop" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <!-- Modal content -->
        <div class="relative bg-white dark:bg-gray-700 rounded-lg shadow max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $post_new->id ? 'Rückmeldung bearbeiten' : 'Neue Rückmeldung erstellen' }}
                </h3>
                <button type="button" id="feedback-modal-close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Body -->
            <div class="p-4 md:p-5">
                <x-posts.form :post="$post_new" :campusers="$campusers_select" :chooseFromUser="true" :route="route('posts.store')"/>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('home.post_delete')
    <script type="module">
        const feedbackModal = document.getElementById('feedback-modal');
        const feedbackModalClose = document.getElementById('feedback-modal-close');
        const feedbackModalBackdrop = document.getElementById('feedback-modal-backdrop');
        const newFeedbackBtn = document.getElementById('new-feedback-btn');
        const standardTextModal = document.getElementById('default-modal');

        function openFeedbackModal() {
            feedbackModal.classList.remove('hidden');
        }

        function closeFeedbackModal() {
            feedbackModal.classList.add('hidden');
        }

        newFeedbackBtn.addEventListener('click', openFeedbackModal);
        feedbackModalClose.addEventListener('click', closeFeedbackModal);
        feedbackModalBackdrop.addEventListener('click', closeFeedbackModal);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !feedbackModal.classList.contains('hidden')) {
                if (standardTextModal && standardTextModal.style.display === 'flex') {
                    return;
                }
                closeFeedbackModal();
            }
        });

        @if($post_new->id)
            openFeedbackModal();
        @endif
    </script>
@endpush
