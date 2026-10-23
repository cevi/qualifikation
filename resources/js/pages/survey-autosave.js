/**
 * Quali ausfüllen (home/survey.blade.php): Antworten und Kommentare speichern sich kurz nach der letzten
 * Änderung selbst. Jedes Formular (.survey-form) für sich, der Speichern-Knopf zeigt den Zustand an.
 */
export const SAVE_DELAY = 1000;

function initForm(form) {
    let timeout = null;
    const saveBtn = form.querySelector('button[name="action"][value="save"]');
    const originalText = saveBtn ? saveBtn.textContent : 'Speichern';

    function showStatus(text, resetAfter) {
        if (saveBtn) {
            saveBtn.textContent = text;
            setTimeout(() => {
                saveBtn.textContent = originalText;
            }, resetAfter);
        }
    }

    function autoSave() {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            if (saveBtn && saveBtn.textContent !== 'Wird gespeichert...') {
                saveBtn.textContent = 'Wird gespeichert...';
            }

            const formData = new FormData(form);
            formData.set('action', 'save');

            fetch(form.getAttribute('action'), {
                method: form.getAttribute('method') || 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            }).then((response) => {
                if (response.ok || response.redirected) {
                    showStatus('Gespeichert ✓', 2000);
                } else {
                    showStatus('Fehler beim Speichern', 3000);
                }
            }).catch((error) => {
                console.error('Error during autosave:', error);
                showStatus('Fehler beim Speichern', 3000);
            });
        }, SAVE_DELAY);
    }

    form.querySelectorAll('input[type="radio"], textarea').forEach((element) => {
        element.addEventListener(element.tagName === 'TEXTAREA' ? 'input' : 'change', autoSave);
    });
}

document.querySelectorAll('.survey-form').forEach(initForm);
