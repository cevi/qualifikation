import Swal from 'sweetalert2';

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
    Swal.fire({ icon: 'error', titleText: 'Das hat nicht geklappt.', text: 'Bitte lade die Seite neu und versuche es nochmals.' });
}

async function createSurveys(url) {
    const result = await Swal.fire({
        titleText: 'Qualifikationen erstellen?',
        text: 'Dies erstellt den Qualifikationsprozess für alle Teilnehmenden deines Kurses, welche ihn noch nicht haben.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ja',
        cancelButtonText: 'Abbrechen',
        confirmButtonColor: 'blue',
        cancelButtonColor: 'red',
    });
    if (result.isConfirmed) {
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
