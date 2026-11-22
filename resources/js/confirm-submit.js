import { confirmDialog } from './lib/dialog.js';

/**
 * Absende-Knöpfe mit Rückfrage auf jeder Seite: data-confirm="Frage" (optional data-confirm-text) fragt im eigenen
 * Dialog nach und schickt erst nach dem Bestätigen das Formular ab, in dem der Knopf steht. Der Bestätigen-Knopf
 * heisst wie der Knopf selbst ("Person löschen"); rot bei button-danger, data-confirm-danger und bei "… löschen".
 * Für Personen, Kurse, Gruppen, Rückmeldungen, die Selbsteinschätzung und «Erste Schritte» (Browser:
 * DeleteConfirmationTest, CampChecklistTest).
 */
export function confirmLabel(button) {
    const text = button.tagName === 'INPUT' ? button.value : button.textContent.trim();

    return button.dataset.confirmLabel || text || button.title || 'OK';
}

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-confirm]');
    if (!button?.form) {
        return;
    }
    event.preventDefault();

    const label = confirmLabel(button);
    const confirmed = await confirmDialog(button.dataset.confirm, {
        text: button.dataset.confirmText,
        confirmLabel: label,
        danger: button.classList.contains('button-danger') || 'confirmDanger' in button.dataset || /löschen/i.test(label),
    });
    if (confirmed) {
        // requestSubmit(button) schickt name/value des Knopfs mit (Quali: action=close)
        button.form.requestSubmit(button);
    }
});
