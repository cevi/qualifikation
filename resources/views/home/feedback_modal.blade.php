{{-- Variables: $post_new, $route, $campusers (optional), $chooseFromUser (optional) --}}
<!-- Feedback Modal -->
<div id="feedback-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="feedback-modal-title">
    <!-- Backdrop -->
    <div id="feedback-modal-backdrop" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <!-- Modal content -->
    <div class="relative bg-white dark:bg-gray-700 rounded-lg shadow max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
            <h3 id="feedback-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ $post_new->id ? 'Rückmeldung bearbeiten' : 'Neue Rückmeldung erstellen' }}
            </h3>
            <button type="button" id="feedback-modal-close" aria-label="Schließen" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <!-- Body -->
        <div class="p-4 md:p-5">
            <x-posts.form :post="$post_new" :campusers="$campusers ?? []" :chooseFromUser="$chooseFromUser ?? false" :route="$route"/>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    const feedbackModal = document.getElementById('feedback-modal');
    const feedbackModalTitle = document.getElementById('feedback-modal-title');
    const feedbackModalClose = document.getElementById('feedback-modal-close');
    const feedbackModalBackdrop = document.getElementById('feedback-modal-backdrop');
    const newFeedbackBtn = document.getElementById('new-feedback-btn');
    const standardTextModal = document.getElementById('default-modal');

    const postIdInput = feedbackModal.querySelector('input[name="post_id"]');
    const commentTextarea = feedbackModal.querySelector('textarea[name="comment"]');
    const showOnSurveyCheckbox = feedbackModal.querySelector('input[name="show_on_survey"]');
    const fileInput = feedbackModal.querySelector('input[name="file"]');
    const deleteFileContainer = feedbackModal.querySelector('#delete-file-container');
    const deleteFileCheckbox = feedbackModal.querySelector('input[name="delete_file"]');
    const deleteFileLabel = feedbackModal.querySelector('label[for="delete_file"]');
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

    newFeedbackBtn.addEventListener('click', function() {
        resetForm();
        feedbackModalTitle.textContent = 'Neue Rückmeldung erstellen';
        openFeedbackModal();
    });

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

    @if($post_new->id ?? false)
        openFeedbackModal();
    @endif
</script>
@endpush
