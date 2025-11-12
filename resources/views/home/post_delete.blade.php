
<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Textbausteine auswählen
                </h3>
                <button type="button" id="cancelBtn" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <table id="optionTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Titel
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Inhalt
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($standard_texts as $ch_key => $standard_text)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" data-value="{{$standard_text->content}}">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$standard_text->title}}
                            </th>
                            <td class="px-6 py-4">
                                {{$standard_text->content}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function(){
        $('.confirm').on('click', function(e){
            e.preventDefault(); //cancel default action

            Swal.fire({
                title: 'Rückmeldung löschen?',
                text: 'Willst du die Rückmeldung wirklich löschen?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ja',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: 'blue',
                cancelButtonColor: 'red',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("DeleteForm").submit();
                }
            });
        });
    });
    const input = document.getElementById('comment');
    const modal = document.getElementById('default-modal');
    const cancelBtn = document.getElementById('cancelBtn');
    const rows = Array.from(document.querySelectorAll('#optionTable tr[data-value]'));
    let selectedIndex = -1;

    let lastChar = '';
    let lastCharTime = 0;


    input.addEventListener('input', (e) => {
        const current = e.data;
        const now = Date.now();
        if (current === '/' && lastChar === '/' && (now - lastCharTime) < 500) {
            // Entfernt die doppelten // aus dem Textfeld
            input.value = input.value.slice(0, -2);
            openModal();
        }
        lastChar = current;
        lastCharTime = now;
    });

    cancelBtn.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
    if (modal.style.display === 'flex') {
        if (e.key === 'Escape') {
        closeModal();
        } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        moveSelection(1);
        } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        moveSelection(-1);
        } else if (e.key === 'Enter') {
        e.preventDefault();
        confirmSelection();
        }
    }
    });

    rows.forEach((row, index) => {
        row.addEventListener('click', () => {
            selectedIndex = index;
            confirmSelection();
        });
    });

    function openModal() {
        modal.style.display = 'flex';
        selectedIndex = 0;
        highlightSelection();
    }

    function closeModal() {
        modal.style.display = 'none';
        clearSelection();
    }

    function moveSelection(direction) {
        selectedIndex += direction;
        if (selectedIndex < 0) selectedIndex = rows.length - 1;
        if (selectedIndex >= rows.length) selectedIndex = 0;
        highlightSelection();
    }

    function highlightSelection() {
        rows.forEach(r => r.classList.add(
            'bg-white',
            'dark:bg-gray-800'
        ));
        rows.forEach(r => r.classList.remove(
            'bg-gray-50',
            'dark:bg-gray-600'
        ));

        if (rows[selectedIndex]) {
            rows[selectedIndex].classList.remove(
            'bg-white',
            'dark:bg-gray-800'
            );
            rows[selectedIndex].classList.add(
            'bg-gray-50',
            'dark:bg-gray-600'
            );
        }
    }

    function clearSelection() {
        selectedIndex = -1;
    }

    function confirmSelection() {
        if (selectedIndex >= 0 && rows[selectedIndex]) {
            const text = rows[selectedIndex].dataset.value;
            input.value += text;
            closeModal();
            input.focus();
        }
    }
</script>
