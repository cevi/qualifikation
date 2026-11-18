/**
 * Quali ausfüllen (home/survey.blade.php): Antworten und Kommentare speichern sich kurz nach der letzten
 * Änderung selbst. Jedes Formular (.survey-form) für sich. Der Speichern-Knopf zeigt den Zustand kurz an, Elemente mit
 * data-save-status dauerhaft ("Gespeichert 18:42", data-tone ok/error für die Farbe).
 */
export const SAVE_DELAY = 1000;

/** Uhrzeit für "Gespeichert 18:42". */
export function savedAt(date = new Date()) {
    return `Gespeichert ${date.toLocaleTimeString('de-CH', { hour: '2-digit', minute: '2-digit' })}`;
}

function initForm(form) {
    let timeout = null;
    const saveBtn = form.querySelector('button[name="action"][value="save"]');
    const originalText = saveBtn ? saveBtn.textContent : 'Speichern';
    const statusElements = [...form.querySelectorAll('[data-save-status]')];

    function setStatus(text, tone) {
        statusElements.forEach((element) => {
            element.textContent = text;
            element.dataset.tone = tone;
        });
    }

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
            setStatus('Wird gespeichert …', 'busy');

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
                    setStatus(savedAt(), 'ok');
                } else {
                    showStatus('Fehler beim Speichern', 3000);
                    setStatus('Nicht gespeichert', 'error');
                }
            }).catch((error) => {
                console.error('Error during autosave:', error);
                showStatus('Fehler beim Speichern', 3000);
                setStatus('Nicht gespeichert', 'error');
            });
        }, SAVE_DELAY);
    }

    form.querySelectorAll('input[type="radio"], textarea').forEach((element) => {
        element.addEventListener(element.tagName === 'TEXTAREA' ? 'input' : 'change', autoSave);
    });
}

document.querySelectorAll('.survey-form').forEach(initForm);
