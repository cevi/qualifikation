import Swal from 'sweetalert2';

/**
 * Löschen-Knöpfe auf jeder Seite: Ein Absende-Knopf mit data-confirm="Titel" (optional data-confirm-text,
 * data-confirm-icon) fragt mit SweetAlert2 nach und schickt erst nach "Ja" das Formular ab, in dem er steht.
 * Ersetzt die jQuery-Kopien in Personen, Kursen, Gruppen und Rückmeldungen (Browser: DeleteConfirmationTest).
 */
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-confirm]');
    if (!button?.form) {
        return;
    }
    event.preventDefault();

    const result = await Swal.fire({
        titleText: button.dataset.confirm,
        text: button.dataset.confirmText,
        icon: button.dataset.confirmIcon ?? 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja',
        cancelButtonText: 'Abbrechen',
        confirmButtonColor: 'blue',
        cancelButtonColor: 'red',
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
    });
    if (result.isConfirmed) {
        button.form.submit();
    }
});
