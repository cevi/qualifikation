/**
 * Rückfragen und Meldungen in einem nativen <dialog> im Stil der Flowbite-Modals. Ersetzt SweetAlert2.
 *
 * - confirmDialog(): Abbrechen ist vorausgewählt (sichere Wahl), der Bestätigen-Knopf wiederholt die Aktion
 *   ("Person löschen"), rot nur bei danger. Esc und Klick neben den Dialog brechen ab.
 *   Mit onConfirm bleibt der Dialog offen, bis die Aktion fertig ist; liefert sie einen Text, steht er als Fehler
 *   im Dialog und man kann es nochmals versuchen (Import aus der Cevi-DB).
 * - messageDialog(): Meldung mit "OK".
 *
 * Texte (auch Namen aus der DB) nur per textContent. Dusk: dialog[data-confirm-dialog][open],
 * [data-dialog-confirm], [data-dialog-cancel].
 */

const BUTTONS = {
    primary:
        'rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-hidden dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
    danger:
        'rounded-lg bg-red-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800 focus:ring-4 focus:ring-red-300 focus:outline-hidden dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900',
    secondary:
        'rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-hidden dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700',
};

// Umrisse wie Heroicons (24 × 24); Farbe des Kreises nach Bedeutung
const ICONS = {
    question: {
        tone: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        path: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 17.25h.008v.008H12v-.008Z',
    },
    warning: {
        tone: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        path: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
    },
    notice: {
        tone: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
        path: 'M12 9v3.75m0 3.75h.008v.008H12v-.008Z',
    },
    info: {
        tone: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        path: 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M12 8.25h.008v.008H12V8.25Z',
    },
};

function element(tag, className, text) {
    const node = document.createElement(tag);
    if (className) {
        node.className = className;
    }
    if (text) {
        node.textContent = text;
    }
    return node;
}

function icon(name) {
    const { tone, path } = ICONS[name];
    const wrapper = element('div', `mx-auto mb-4 grid size-12 place-items-center rounded-full ${tone}`);
    wrapper.setAttribute('aria-hidden', 'true');
    wrapper.innerHTML = `<svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="${path}"/></svg>`;
    return wrapper;
}

function button(label, variant, attribute) {
    const node = element('button', BUTTONS[variant], label);
    node.type = 'button';
    node.setAttribute(attribute, '');
    return node;
}

/** Baut den Dialog; buttons in der Reihenfolge auf dem Desktop (auf dem Handy untereinander, Hauptknopf oben). */
function build({ title, text, items, iconName, buttons }) {
    const dialog = element(
        'dialog',
        'm-auto w-[calc(100%-2rem)] max-w-md rounded-lg border-0 bg-white p-0 text-gray-900 shadow-xl backdrop:bg-gray-900/60 dark:bg-gray-800 dark:text-white',
    );
    dialog.setAttribute('data-confirm-dialog', '');
    const id = `dialog-title-${Math.random().toString(36).slice(2)}`;
    dialog.setAttribute('aria-labelledby', id);

    const body = element('div', 'p-5 text-center sm:p-6');
    body.append(icon(iconName));
    const heading = element('h2', 'mb-2 text-lg font-semibold text-gray-900 dark:text-white', title);
    heading.id = id;
    body.append(heading);
    if (text) {
        body.append(element('p', 'mb-0 text-sm text-gray-500 dark:text-gray-400', text));
    }
    if (items?.length) {
        const list = element('ul', 'mx-auto mt-3 mb-0 max-h-48 list-disc overflow-y-auto pl-5 text-left text-sm text-gray-700 dark:text-gray-300');
        items.forEach((item) => list.append(element('li', '', item)));
        body.append(list);
    }
    const error = element('p', 'mt-3 mb-0 hidden text-sm font-medium text-red-600 dark:text-red-500');
    error.setAttribute('role', 'alert');
    body.append(error);

    const row = element('div', 'mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-center');
    row.append(...buttons);
    body.append(row);
    dialog.append(body);

    return { dialog, error };
}

function open(dialog, focus) {
    document.body.append(dialog);
    dialog.showModal();
    // showModal() fokussiert selbst das erste Element; danach bewusst setzen
    focus.focus();
}

/**
 * @param {string} title Frage ("Person löschen?")
 * @param {{text?: string, items?: string[], confirmLabel?: string, danger?: boolean, onConfirm?: () => Promise<string|void>}} options
 * @returns {Promise<boolean>} true nach "Bestätigen" (und erfolgreichem onConfirm)
 */
export function confirmDialog(title, { text = '', items = [], confirmLabel = 'OK', danger = false, onConfirm } = {}) {
    const cancel = button('Abbrechen', 'secondary', 'data-dialog-cancel');
    const confirm = button(confirmLabel, danger ? 'danger' : 'primary', 'data-dialog-confirm');
    const { dialog, error } = build({ title, text, items, iconName: danger ? 'warning' : 'question', buttons: [cancel, confirm] });
    let busy = false;

    return new Promise((resolve) => {
        function finish(result) {
            dialog.close();
            dialog.remove();
            resolve(result);
        }

        cancel.addEventListener('click', () => !busy && finish(false));
        // Klick auf den abgedunkelten Hintergrund trifft das <dialog> selbst
        dialog.addEventListener('click', (event) => event.target === dialog && !busy && finish(false));
        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            if (!busy) {
                finish(false);
            }
        });
        confirm.addEventListener('click', async () => {
            if (!onConfirm) {
                finish(true);
                return;
            }
            busy = true;
            confirm.disabled = cancel.disabled = true;
            confirm.textContent = 'Bitte warten …';
            error.classList.add('hidden');
            let message;
            try {
                message = await onConfirm();
            } catch {
                message = 'Das hat nicht geklappt. Bitte versuche es nochmals.';
            }
            busy = false;
            if (message) {
                error.textContent = message;
                error.classList.remove('hidden');
                confirm.disabled = cancel.disabled = false;
                confirm.textContent = confirmLabel;
                return;
            }
            finish(true);
        });

        open(dialog, cancel);
    });
}

/**
 * @param {string} title
 * @param {{text?: string, items?: string[], tone?: 'info'|'notice'|'warning'}} options
 * @returns {Promise<void>} nach "OK", Esc oder Klick daneben
 */
export function messageDialog(title, { text = '', items = [], tone = 'info' } = {}) {
    const ok = button('OK', 'primary', 'data-dialog-confirm');
    const { dialog } = build({ title, text, items, iconName: tone, buttons: [ok] });

    return new Promise((resolve) => {
        function finish() {
            dialog.close();
            dialog.remove();
            resolve();
        }

        ok.addEventListener('click', finish);
        dialog.addEventListener('click', (event) => event.target === dialog && finish());
        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            finish();
        });

        open(dialog, ok);
    });
}
