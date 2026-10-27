import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

/**
 * Profilbild zuschneiden (admin/users/create und edit, Komponente x-users.photo-cropper): Nach der Wahl einer Datei
 * im Feld mit Klasse "photo" öffnet ein <dialog>; "Zuschneiden" legt das Bild (300×300, PNG als data-URL) in
 * #cropped_photo_id. Abbrechen, Kreuz und Escape schliessen ohne Änderung. Browser-Test: PhotoCropperTest.
 */
const dialog = document.getElementById('photo-cropper');

if (dialog) {
    const image = dialog.querySelector('img');
    const field = document.getElementById('cropped_photo_id');
    let cropper = null;

    document.querySelectorAll('input.photo').forEach((input) =>
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) {
                return;
            }
            image.src = URL.createObjectURL(file);
            dialog.showModal();
            cropper = new Cropper(image, { aspectRatio: 1, viewMode: 2, preview: '.preview' });
        }),
    );

    dialog.addEventListener('close', () => {
        cropper?.destroy();
        cropper = null;
        URL.revokeObjectURL(image.src);
    });

    dialog.querySelector('[data-action="crop"]').addEventListener('click', () => {
        field.value = cropper.getCroppedCanvas({ width: 300, height: 300 }).toDataURL();
        dialog.close();
    });

    dialog.querySelectorAll('[data-action="close"]').forEach((button) => button.addEventListener('click', () => dialog.close()));
}
