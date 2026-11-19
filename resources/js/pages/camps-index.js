/**
 * Kursübersicht (admin/camps/index.blade.php): Dialoge «Neuer Kurs» und «Kurs bearbeiten» als native <dialog>.
 *
 * - «Neuer Kurs» öffnet #camp-create-btn.
 * - «Kurs bearbeiten» öffnet ein Klick auf den Kursnamen der Tabelle (a[data-open-dialog]); die Werte kommen als
 *   JSON von derselben Adresse (AdminCampsController@edit). Ctrl-/Cmd-Klick öffnet die Adresse normal, dort ist
 *   der Dialog beim Laden offen (data-camp), ebenso bei Links aus «Erste Schritte» und der Kursauswahl.
 * - Kursleitung: nur die Personen, die der Server liefert (Rolle Kursleitung in diesem Kurs); Namen per textContent.
 * - «Kurs abschliessen / löschen» im Bearbeiten-Dialog (und als Knopf in der Tabelle), nur wenn can_finish (offen,
 *   nicht das Global-Camp); die Rückfrage stellt confirm-submit.js.
 */
function setupDialog(dialog, onClose) {
    dialog.querySelectorAll('[data-dialog-close]').forEach((button) => button.addEventListener('click', () => dialog.close()));
    // Klick auf den abgedunkelten Hintergrund trifft das <dialog> selbst
    dialog.addEventListener('click', (event) => event.target === dialog && dialog.close());
    if (onClose) {
        dialog.addEventListener('close', onClose);
    }
}

export function fillEditDialog(dialog, camp) {
    const form = dialog.querySelector('#CampEditForm');
    const field = (name) => form.querySelector(`[name="${name}"]`);

    form.action = camp.update_url;
    dialog.querySelector('#camp-edit-title').textContent = camp.name ? `${camp.name} bearbeiten` : 'Kurs bearbeiten';
    field('name').value = camp.name ?? '';
    field('camp_type_id').value = String(camp.camp_type_id ?? '');
    field('group_id').value = String(camp.group_id ?? '');
    field('end_date').value = camp.end_date ?? '';
    field('status_control').checked = Boolean(camp.status_control);
    const foreignId = field('foreign_id');
    if (foreignId) {
        foreignId.value = camp.foreign_id ?? '';
    }

    const leader = field('user_id');
    const leaders = camp.leaders ?? [];
    const option = (label, value) => {
        const node = document.createElement('option');
        node.value = value;
        node.textContent = label;
        return node;
    };
    const empty = option(leaders.length ? 'Keine Kursleitung' : 'Keine Person mit Rolle Kursleitung im Kurs', '');
    leader.replaceChildren(empty, ...leaders.map(({ id, name }) => option(name, String(id))));
    const current = leaders.some(({ id }) => id === camp.user_id) ? String(camp.user_id) : '';
    leader.value = current;

    const finish = dialog.querySelector('[data-finish-section]');
    finish.querySelector('form').action = camp.destroy_url;
    finish.classList.toggle('hidden', !camp.can_finish);
}

export function initCampsIndex(root = document) {
    const createDialog = root.getElementById('camp-create-dialog');
    const editDialog = root.getElementById('camp-edit-dialog');
    if (!createDialog || !editDialog) {
        return null;
    }

    setupDialog(createDialog);
    // Kam die Seite über /admin/camps/{id}/edit, nach dem Schliessen wieder die Adresse der Übersicht zeigen
    setupDialog(editDialog, () => {
        const indexUrl = editDialog.dataset.indexUrl;
        if (indexUrl && window.location.href !== indexUrl) {
            window.history.replaceState({}, '', indexUrl);
        }
    });

    root.getElementById('camp-create-btn')?.addEventListener('click', () => {
        createDialog.showModal();
        createDialog.querySelector('[name="name"]')?.focus();
    });

    function openEdit(camp) {
        fillEditDialog(editDialog, camp);
        editDialog.showModal();
        editDialog.querySelector('[name="name"]').focus();
    }

    root.addEventListener('click', async (event) => {
        const link = event.target.closest('a[data-open-dialog]');
        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }
        event.preventDefault();
        try {
            const response = await fetch(link.href, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            openEdit(await response.json());
        } catch {
            // Ohne Daten die Seite mit offenem Dialog laden
            window.location.href = link.href;
        }
    });

    if (editDialog.dataset.camp) {
        openEdit(JSON.parse(editDialog.dataset.camp));
    }

    return { openEdit };
}

initCampsIndex();
