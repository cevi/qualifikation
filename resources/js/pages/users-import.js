import { confirmDialog, messageDialog } from '../lib/dialog.js';

/**
 * Personenliste (admin/users/index.blade.php): Import aus der Cevi-DB. Fehler des Servers ("Keine Kurs-ID …") stehen
 * im Dialog, man kann es nochmals versuchen. Personen ohne E-Mail-Adresse überspringt der Server und liefert ihre
 * Namen zurück; die kommen aus der Cevi-DB und werden als Text angezeigt.
 */
async function startImport(url) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let skipped = [];

    const confirmed = await confirmDialog('Personen aus Cevi-DB importieren', {
        text: 'Voraussetzung: Dem Kurs sind eine Gruppe und eine Kurs-ID der Cevi-DB zugewiesen. Erstellte Personen können sich über die Cevi-DB anmelden.',
        confirmLabel: 'Importieren',
        onConfirm: async () => {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const body = await response.json().catch(() => ({}));
            if (!response.ok) {
                return body.error || 'Der Import hat nicht geklappt. Bitte versuche es nochmals.';
            }
            skipped = body.skipped ?? [];
        },
    });
    if (!confirmed) {
        return;
    }
    if (skipped.length) {
        // Personen ohne E-Mail-Adresse in der Cevi-DB können nicht angelegt werden.
        await messageDialog('Import abgeschlossen', {
            text: `${skipped.length} Person(en) wurden übersprungen, weil sie in der Cevi-DB keine E-Mail-Adresse haben:`,
            items: skipped,
            tone: 'notice',
        });
    }
    window.location.reload();
}

const button = document.getElementById('showImport');
button?.addEventListener('click', () => startImport(button.dataset.url));
