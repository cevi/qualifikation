/**
 * Foto vergrössern (Profil): Knopf mit data-photo-open="<id>" öffnet den <dialog> mit dieser id. Schliessen über den
 * Knopf im Dialog (form method="dialog"), Esc oder einen Klick neben das Bild.
 */
export function initPhotoDialogs(root = document) {
    root.querySelectorAll('[data-photo-open]').forEach((button) => {
        const dialog = root.getElementById(button.dataset.photoOpen);
        if (!dialog) {
            return;
        }
        button.addEventListener('click', () => dialog.showModal());
        // Der Klick auf den Hintergrund trifft den Dialog selbst, nicht seinen Inhalt
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
}

initPhotoDialogs();
