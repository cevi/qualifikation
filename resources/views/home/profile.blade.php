@extends('layouts.layout')
@section('survey_content')
    {{--@include('includes.tinyeditor')--}}
    <div class="row">
        <x-layouts.page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-body cardbody-navtabs">
                    <p>
                        Diese Seite ist für Teilnehmer nicht sichtbar.
                    </p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="text-center-profile mbl">
                                    <img src="{{$user->getAvatar()}}" alt="img"
                                            class="img-circle img-bor">
                                </div>
                            </div>
                            <div class="profile_user">
                                <h3 class="user_name_max">{{$user->username}} {{$user->group['shortname'] ?? ''}}</h3>
                                <p>{{$user->leader ? $user->leader->username : ''}}</p>
                            </div>
                            <br>
                            <div class="text-center-profile mbl">
                                <div class="ampel" id="ampel">
                                    <a href="javascript:" class="ampel-btn"
                                        data-color="{{config('status.classification_red')}}"
                                        data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_red')])}}'>
                                        <div
                                            class="circle {{$camp_user->classification_id == config('status.classification_red') ? 'red' : ''}}"></div>
                                    </a>
                                    <a href="javascript:" class="ampel-btn"
                                        data-color="{{config('status.classification_yellow')}}"
                                        data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_yellow')])}}'>
                                        <div
                                            class="circle {{$camp_user->classification_id == config('status.classification_yellow') ? 'yellow' : ''}}"></div>
                                    </a>
                                    <a href="javascript:" class="ampel-btn"
                                        data-color="{{config('status.classification_green')}}"
                                        data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_green')])}}'>
                                        <div
                                            class="circle {{$camp_user->classification_id == config('status.classification_green') ? 'green' : ''}}"></div>
                                    </a>
                                </div>
                            </div>
                            @foreach($surveys as $survey)
                                <x-surveys.radar-chart/>
                            @endforeach
                        </div>
                        <div class="col-lg-6">

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :user="$user"/>
                                    <button type="button" id="new-feedback-btn" class="btn btn-primary mt-3">
                                        Neue Rückmeldung
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <h3 id="feedback-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                    Neue Rückmeldung erstellen
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
                <x-posts.form :post="$post_new" :route="route('profile.post.store', $user)"/>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    @include('home.radar')
    @include('home.post_delete')
    <script type="module">
        $('.ampel-btn').on('click', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var url = $(this).data('remote');
            var color = $(this).data('color');
            $.ajax({
                url: url,
                type: 'PATCH',
                data: {},
                success: function (res) {
                    location.reload();
                }
            });
        });

        // Feedback Modal Logic
        const feedbackModal = document.getElementById('feedback-modal');
        const feedbackModalTitle = document.getElementById('feedback-modal-title');
        const feedbackModalClose = document.getElementById('feedback-modal-close');
        const feedbackModalBackdrop = document.getElementById('feedback-modal-backdrop');
        const newFeedbackBtn = document.getElementById('new-feedback-btn');
        const standardTextModal = document.getElementById('default-modal');

        // Form elements inside modal
        const postIdInput = feedbackModal.querySelector('input[name="post_id"]');
        const commentTextarea = feedbackModal.querySelector('textarea[name="comment"]');
        const showOnSurveyCheckbox = feedbackModal.querySelector('input[name="show_on_survey"]');
        const fileInput = feedbackModal.querySelector('input[name="file"]');
        const deleteFileContainer = feedbackModal.querySelector('#delete-file-container');
        const deleteFileCheckbox = feedbackModal.querySelector('input[name="delete_file"]');
        const deleteFileLabel = feedbackModal.querySelector('#delete-file-label');
        const submitBtn = feedbackModal.querySelector('button[type="submit"]');

        function openFeedbackModal() {
            feedbackModal.classList.remove('hidden');
        }

        function closeFeedbackModal() {
            feedbackModal.classList.add('hidden');
        }

        function resetForm() {
            postIdInput.value = '';
            commentTextarea.value = '';
            showOnSurveyCheckbox.checked = false;
            fileInput.value = '';
            if (deleteFileContainer) {
                deleteFileContainer.classList.add('hidden');
            }
            if (deleteFileCheckbox) {
                deleteFileCheckbox.checked = false;
            }
            submitBtn.textContent = 'Rückmeldung Erstellen';
        }

        // New feedback button
        newFeedbackBtn.addEventListener('click', function() {
            resetForm();
            feedbackModalTitle.textContent = 'Neue Rückmeldung erstellen';
            openFeedbackModal();
        });

        // Edit buttons (delegated)
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-post-btn');
            if (editBtn) {
                e.preventDefault();
                const postData = JSON.parse(editBtn.dataset.post);

                postIdInput.value = postData.id;
                commentTextarea.value = postData.comment;
                showOnSurveyCheckbox.checked = postData.showOnSurvey;
                fileInput.value = '';

                if (deleteFileContainer) {
                    if (postData.file) {
                        deleteFileContainer.classList.remove('hidden');
                        if (deleteFileLabel) {
                            deleteFileLabel.textContent = 'Bestehende Datei ' + postData.filename + ' löschen';
                        }
                    } else {
                        deleteFileContainer.classList.add('hidden');
                    }
                }
                if (deleteFileCheckbox) {
                    deleteFileCheckbox.checked = false;
                }

                submitBtn.textContent = 'Änderungen Speichern';
                feedbackModalTitle.textContent = 'Rückmeldung bearbeiten';
                openFeedbackModal();
            }
        });

        // Close button
        feedbackModalClose.addEventListener('click', closeFeedbackModal);

        // Backdrop click
        feedbackModalBackdrop.addEventListener('click', closeFeedbackModal);

        // ESC key (only if standard text modal is not open)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !feedbackModal.classList.contains('hidden')) {
                // Check if standard text modal is open
                if (standardTextModal && standardTextModal.style.display === 'flex') {
                    return; // Let the standard text modal handle ESC
                }
                closeFeedbackModal();
            }
        });
    </script>
@endpush

