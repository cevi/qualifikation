<h2 class="text-xl font-semibold sm:text-2xl dark:text-white" id="accordion-flush-heading-{{$chapter->chapter['number']}}">
    <button type="button"
            class="flex items-center gap-3 my-4 justify-between w-full p-3 font-semibold text-left text-gray-500 border border-b-0 border-gray-200 rounded-xl dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
            data-accordion-target="#accordion-flush-body-{{$chapter->chapter['number']}}"
            aria-expanded="false"
            aria-controls="accordion-flush-body-{{$chapter->chapter['number']}}">
        <span class="min-w-0 hyphens-auto wrap-break-word">{{$chapter->chapter['number']}}. {{$chapter->chapter['name']}}</span>
        <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor"
             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"></path>
        </svg>
    </button>
</h2>
