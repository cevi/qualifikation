{{-- Variables: $post_new, $route, $campusers (optional), $chooseFromUser (optional) --}}
<!-- Feedback Modal -->
<div id="feedback-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="feedback-modal-title"
     data-open-on-load="{{ ($post_new->id ?? false) ? 'true' : 'false' }}">
    <!-- Backdrop -->
    <div id="feedback-modal-backdrop" class="fixed inset-0 bg-black/50"></div>
    <!-- Modal content -->
    <div class="relative bg-white dark:bg-gray-700 rounded-lg shadow max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
            <h3 id="feedback-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ $post_new->id ? 'Rückmeldung bearbeiten' : 'Neue Rückmeldung erstellen' }}
            </h3>
            <button type="button" id="feedback-modal-close" aria-label="Schliessen" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Schliessen</span>
            </button>
        </div>
        <!-- Body -->
        <div class="p-4 md:p-5">
            <x-posts.form :post="$post_new" :campusers="$campusers ?? []" :questions="$questions ?? []" :chooseFromUser="$chooseFromUser ?? false" :route="$route"/>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/pages/feedback-modal.js')
@endpush
