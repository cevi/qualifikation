{{-- Profilbild zuschneiden: öffnet sich nach der Wahl einer Datei im Feld mit Klasse "photo", das Ergebnis landet
     in #cropped_photo_id (resources/js/pages/photo-cropper.js). --}}
<dialog id="photo-cropper" aria-labelledby="photo-cropper-title"
        class="m-auto w-[calc(100%-2rem)] max-w-4xl rounded-lg bg-white p-0 shadow-xs backdrop:bg-gray-900/50 dark:bg-gray-700">
    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
        <h3 id="photo-cropper-title" class="text-xl font-semibold text-gray-900 dark:text-white">Vorschaubild zuschneiden</h3>
        <button type="button" data-action="close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Schliessen</span>
        </button>
    </div>
    <div class="grid gap-4 p-4 md:grid-cols-3 md:p-5">
        <div class="md:col-span-2">
            <img alt="Gewähltes Bild" class="max-h-[60vh]">
        </div>
        <div class="preview"></div>
    </div>
    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button type="button" data-action="close" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-hidden focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">Abbrechen</button>
        <button type="button" data-action="crop" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">Zuschneiden</button>
    </div>
</dialog>
@push('scripts')
    @vite('resources/js/pages/photo-cropper.js')
@endpush
