import { beforeEach, describe, expect, it, vi } from 'vitest';

// Aufbau wie home/feedback_modal.blade.php mit x-posts.form
function render({ openOnLoad = false } = {}) {
    document.body.innerHTML = `
        <button id="new-feedback-btn">Neue Rückmeldung</button>
        <button class="edit-post-btn" data-post='{"id":7,"comment":"Gut gemacht","showOnSurvey":true,"file":true,"filename":"plan.pdf"}'>
            <i>Bearbeiten</i>
        </button>
        <div id="default-modal" style="display: none"></div>
        <div id="feedback-modal" class="hidden" data-open-on-load="${openOnLoad}">
            <div id="feedback-modal-backdrop"></div>
            <h3 id="feedback-modal-title">Neue Rückmeldung erstellen</h3>
            <button id="feedback-modal-close">x</button>
            <form>
                <input type="hidden" name="post_id" value="">
                <textarea name="comment"></textarea>
                <input type="checkbox" name="show_on_survey">
                <input type="file" name="file">
                <div id="delete-file-container" class="hidden">
                    <input type="checkbox" name="delete_file" id="delete_file"><label for="delete_file">Bestehende Datei löschen</label>
                </div>
                <button type="submit">Rückmeldung erstellen</button>
            </form>
        </div>`;
}

async function load(search = '') {
    window.history.replaceState({}, '', `/posts${search}`);
    vi.resetModules();
    await import('../../resources/js/pages/feedback-modal.js');
}

const modal = () => document.getElementById('feedback-modal');
const field = (selector) => modal().querySelector(selector);
const isOpen = () => !modal().classList.contains('hidden');

describe('Fenster Rückmeldung', () => {
    beforeEach(() => render());

    it('öffnet leer für eine neue Rückmeldung', async () => {
        await load();
        expect(isOpen()).toBe(false);

        document.getElementById('new-feedback-btn').click();

        expect(isOpen()).toBe(true);
        expect(field('textarea').value).toBe('');
        expect(document.getElementById('feedback-modal-title').textContent).toBe('Neue Rückmeldung erstellen');
        expect(field('button[type="submit"]').textContent).toBe('Rückmeldung erstellen');
        expect(document.activeElement).toBe(field('textarea'));
    });

    it('füllt beim Bearbeiten Text, Häkchen und Datei aus data-post', async () => {
        await load();

        document.querySelector('.edit-post-btn i').click();

        expect(isOpen()).toBe(true);
        expect(field('input[name="post_id"]').value).toBe('7');
        expect(field('textarea').value).toBe('Gut gemacht');
        expect(field('input[name="show_on_survey"]').checked).toBe(true);
        expect(field('#delete-file-container').classList.contains('hidden')).toBe(false);
        expect(field('label[for="delete_file"]').textContent).toBe('Bestehende Datei plan.pdf löschen');
        expect(field('button[type="submit"]').textContent).toBe('Änderungen speichern');

        // Danach wieder neu: alles leer, Datei-Feld weg
        document.getElementById('new-feedback-btn').click();
        expect(field('input[name="post_id"]').value).toBe('');
        expect(field('#delete-file-container').classList.contains('hidden')).toBe(true);
    });

    it('schliesst mit Escape, Kreuz und Hintergrund, aber nicht bei offenen Textbausteinen', async () => {
        await load();
        document.getElementById('new-feedback-btn').click();

        document.getElementById('default-modal').style.display = 'flex';
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        expect(isOpen()).toBe(true);

        document.getElementById('default-modal').style.display = 'none';
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        expect(isOpen()).toBe(false);

        document.getElementById('new-feedback-btn').click();
        document.getElementById('feedback-modal-close').click();
        expect(isOpen()).toBe(false);

        document.getElementById('new-feedback-btn').click();
        document.getElementById('feedback-modal-backdrop').click();
        expect(isOpen()).toBe(false);
    });

    it('öffnet mit ?neu direkt ein leeres Formular', async () => {
        await load('?neu=1');

        expect(isOpen()).toBe(true);
        expect(document.activeElement).toBe(field('textarea'));
    });

    it('öffnet beim Bearbeiten-Aufruf ohne die Eingaben vom Server zu leeren', async () => {
        render({ openOnLoad: true });
        field('textarea').value = 'Vom Server';

        await load();

        expect(isOpen()).toBe(true);
        expect(field('textarea').value).toBe('Vom Server');
    });
});
