import Swal from 'sweetalert2';
import { escapeHtml } from '../lib/escape-html.js';

/**
 * Personenliste (admin/users/index.blade.php): Import aus der Cevi-DB. Der Server überspringt Personen ohne
 * E-Mail-Adresse und liefert ihre Namen zurück; die kommen aus der Cevi-DB und werden als Text angezeigt.
 */
function startImport(url) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    Swal.fire({
        title: 'Personen aus Cevi-DB importieren',
        html: `<div class="p-4 md:p-5 space-y-4">
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                Vorraussetzungen für DB-Import:
            </p>
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <ul>
                    <li>Zugewiesene Gruppe für den Kurs.</li>
                    <li>Zugewiesene Kurs-ID für den Kurs.</li>
                </ul>
            </p>
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">Erstellte Personen können sich über die Cevi-DB anmelden.</p>
        </div>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Importieren',
        cancelButtonText: 'Abbrechen',
        confirmButtonColor: 'blue',
        cancelButtonColor: 'red',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });

                if (!response.ok) {
                    const responseJSON = await response.json();
                    return Swal.showValidationMessage(responseJSON.error);
                }
                return response.json();
            } catch (error) {
                Swal.showValidationMessage(`Request failed: ${error}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        const skipped = result.value?.skipped;
        if (!skipped || skipped.length === 0) {
            window.location.reload();
            return;
        }
        // Personen ohne E-Mail-Adresse in der Cevi-DB können nicht angelegt werden.
        Swal.fire({
            title: 'Import abgeschlossen',
            icon: 'warning',
            html: `<p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    ${skipped.length} Person(en) wurden übersprungen, weil sie in der
                    Cevi-DB keine E-Mail-Adresse haben:
                </p>
                <ul class="mt-2 text-left">${skipped.map((name) => `<li>${escapeHtml(name)}</li>`).join('')}</ul>`,
            confirmButtonText: 'OK',
            confirmButtonColor: 'blue',
        }).then(() => window.location.reload());
    });
}

const button = document.getElementById('showImport');
button?.addEventListener('click', () => startImport(button.dataset.url));
