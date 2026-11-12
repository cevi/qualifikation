import { confirmDialog, messageDialog } from '../lib/dialog.js';

/**
 * Quali-Liste (admin/surveys/index.blade.php): "Qualifikationen erstellen" fragt nach, "… Selbsteinschätzung
 * freigeben" nicht. Beide schicken einen POST und laden die Seite neu. Adressen aus #surveys-index-config.
 */
async function post(url) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        });
        if (response.ok) {
            window.location.reload();
            return;
        }
    } catch {
        // wie bei einer Fehlerantwort
    }
    messageDialog('Das hat nicht geklappt.', { text: 'Bitte lade die Seite neu und versuche es nochmals.', tone: 'warning' });
}

async function createSurveys(url) {
    const confirmed = await confirmDialog('Qualifikationen erstellen?', {
        text: 'Dies erstellt den Qualifikationsprozess für alle Teilnehmenden deines Kurses, welche ihn noch nicht haben.',
        confirmLabel: 'Qualifikationen erstellen',
    });
    if (confirmed) {
        await post(url);
    }
}

const configElement = document.getElementById('surveys-index-config');
if (configElement) {
    const config = JSON.parse(configElement.textContent);
    const actions = {
        create: () => createSurveys(config.createUrl),
        opensurvey: () => post(config.openSurveyUrl),
    };
    // Knöpfe mit Klasse "create" bzw. "opensurvey" (auch im leeren Zustand und in x-surveys.survey-button)
    Object.entries(actions).forEach(([className, action]) => {
        document.querySelectorAll(`.${className}`).forEach((button) =>
            button.addEventListener('click', (event) => {
                event.preventDefault();
                action();
            }),
        );
    });
}
