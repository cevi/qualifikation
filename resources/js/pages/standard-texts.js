/**
 * Textbausteine in Rückmeldungen (home/standard-texts-modal.blade.php): "//" im Kommentarfeld öffnet die Auswahl,
 * Pfeiltasten wählen, Enter oder Klick fügt den Text an der Stelle der "//" ein, Escape oder das Kreuz schliesst.
 */
export const DOUBLE_SLASH_WINDOW = 500;

function init() {
    const input = document.getElementById('comment');
    const modal = document.getElementById('default-modal');
    const cancelBtn = document.getElementById('cancelBtn');
    const rows = Array.from(document.querySelectorAll('#optionTable tr[data-value]'));
    if (!input || !modal) {
        return;
    }

    let selectedIndex = -1;
    let lastChar = '';
    let lastCharTime = 0;
    // Stelle im Text, an der die // standen; dort kommt der Baustein hin.
    let insertAt = 0;

    function openModal() {
        modal.style.display = 'flex';
        selectedIndex = 0;
        highlightSelection();
    }

    function closeModal() {
        modal.style.display = 'none';
        selectedIndex = -1;
    }

    function moveSelection(direction) {
        selectedIndex += direction;
        if (selectedIndex < 0) selectedIndex = rows.length - 1;
        if (selectedIndex >= rows.length) selectedIndex = 0;
        highlightSelection();
    }

    function highlightSelection() {
        rows.forEach((row, index) => {
            const selected = index === selectedIndex;
            row.classList.toggle('bg-white', !selected);
            row.classList.toggle('dark:bg-gray-800', !selected);
            row.classList.toggle('bg-gray-50', selected);
            row.classList.toggle('dark:bg-gray-600', selected);
        });
    }

    function confirmSelection() {
        if (selectedIndex >= 0 && rows[selectedIndex]) {
            const text = rows[selectedIndex].dataset.value;
            input.value = input.value.slice(0, insertAt) + text + input.value.slice(insertAt);
            closeModal();
            input.focus();
            input.setSelectionRange(insertAt + text.length, insertAt + text.length);
        }
    }

    input.addEventListener('input', (e) => {
        const current = e.data;
        const now = Date.now();
        const cursor = input.selectionStart ?? input.value.length;
        if (current === '/' && lastChar === '/' && (now - lastCharTime) < DOUBLE_SLASH_WINDOW
            && input.value.slice(cursor - 2, cursor) === '//') {
            // Die beiden / vor dem Cursor entfernen, an ihrer Stelle wird eingefügt
            insertAt = cursor - 2;
            input.value = input.value.slice(0, insertAt) + input.value.slice(cursor);
            input.setSelectionRange(insertAt, insertAt);
            openModal();
            // Das nächste / beginnt ein neues Paar
            lastChar = '';
            return;
        }
        lastChar = current;
        lastCharTime = now;
    });

    cancelBtn?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (modal.style.display !== 'flex') {
            return;
        }
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
    });

    rows.forEach((row, index) => {
        row.addEventListener('click', () => {
            selectedIndex = index;
            confirmSelection();
        });
    });
}

init();
