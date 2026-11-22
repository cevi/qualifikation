import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

// Aufbau wie admin/camps/index.blade.php (gekürzt)
function render({ camp = null } = {}) {
    document.body.innerHTML = `
        <button id="camp-create-btn">Neuer Kurs</button>
        <a href="/admin/camps/7/edit" data-open-dialog>Kurs A</a>
        <dialog id="camp-create-dialog">
            <button type="button" data-dialog-close>x</button>
            <form id="CampCreateForm"><input name="name"></form>
        </dialog>
        <dialog id="camp-edit-dialog" data-index-url="${window.location.origin}/admin/camps"
            ${camp ? `data-camp='${JSON.stringify(camp)}'` : ''}>
            <h2 id="camp-edit-title">Kurs bearbeiten</h2>
            <button type="button" data-dialog-close>x</button>
            <form id="CampEditForm" action="">
                <input name="name">
                <select name="user_id"></select>
                <select name="camp_type_id"><option value=""></option><option value="1">J+S</option><option value="2">Kader</option></select>
                <input type="checkbox" name="status_control">
                <input type="date" name="end_date">
                <select name="group_id"><option value=""></option><option value="3">Gruppe</option></select>
            </form>
            <div data-finish-section>
                <form id="DeleteForm" action=""><button class="confirm">Kurs abschliessen / löschen</button></form>
            </div>
        </dialog>`;
    document.querySelectorAll('dialog').forEach((dialog) => {
        // happy-dom kennt showModal/close nur teilweise: als Attrappe, die das open-Attribut setzt
        dialog.showModal = vi.fn(() => dialog.setAttribute('open', ''));
        dialog.close = vi.fn(() => {
            dialog.removeAttribute('open');
            dialog.dispatchEvent(new Event('close'));
        });
    });
}

const CAMP = {
    id: 7,
    name: 'Kurs A',
    user_id: 12,
    camp_type_id: 2,
    group_id: 3,
    status_control: true,
    end_date: '2026-12-01',
    foreign_id: null,
    can_finish: true,
    leaders: [
        { id: 12, name: 'Zora' },
        { id: 4, name: '<b>Anna</b>' },
    ],
    update_url: '/admin/camps/7',
    destroy_url: '/admin/camps/7',
};

async function load() {
    vi.resetModules();
    return import('../../resources/js/pages/camps-index.js');
}

const edit = () => document.getElementById('camp-edit-dialog');
const field = (name) => edit().querySelector(`[name="${name}"]`);

describe('Kursübersicht: Dialoge', () => {
    beforeEach(() => window.history.replaceState({}, '', '/admin/camps'));
    afterEach(() => vi.unstubAllGlobals());

    it('öffnet «Neuer Kurs» über den Knopf und schliesst ihn wieder', async () => {
        render();
        await load();
        const create = document.getElementById('camp-create-dialog');

        document.getElementById('camp-create-btn').click();
        expect(create.showModal).toHaveBeenCalledTimes(1);

        create.querySelector('[data-dialog-close]').click();
        expect(create.hasAttribute('open')).toBe(false);
    });

    it('lädt beim Klick auf den Kursnamen die Daten und füllt den Bearbeiten-Dialog', async () => {
        render();
        const fetchMock = vi.fn().mockResolvedValue({ ok: true, json: () => Promise.resolve(CAMP) });
        vi.stubGlobal('fetch', fetchMock);
        await load();

        document.querySelector('a[data-open-dialog]').click();
        await vi.waitFor(() => expect(edit().showModal).toHaveBeenCalled());

        expect(fetchMock).toHaveBeenCalledWith(expect.stringContaining('/admin/camps/7/edit'), {
            headers: { Accept: 'application/json' },
        });
        expect(document.getElementById('camp-edit-title').textContent).toBe('Kurs A bearbeiten');
        expect(document.getElementById('CampEditForm').getAttribute('action')).toBe('/admin/camps/7');
        expect(document.getElementById('DeleteForm').getAttribute('action')).toBe('/admin/camps/7');
        expect(field('name').value).toBe('Kurs A');
        expect(field('camp_type_id').value).toBe('2');
        expect(field('group_id').value).toBe('3');
        expect(field('end_date').value).toBe('2026-12-01');
        expect(field('status_control').checked).toBe(true);
    });

    it('bietet als Kursleitung nur die gelieferten Personen an, Namen als Text', async () => {
        render({ camp: CAMP });
        await load();

        const options = [...field('user_id').options].map((option) => [option.value, option.textContent]);
        expect(options).toEqual([
            ['', 'Keine Kursleitung'],
            ['12', 'Zora'],
            ['4', '<b>Anna</b>'],
        ]);
        expect(field('user_id').value).toBe('12');
        expect(field('user_id').querySelector('b')).toBeNull();
    });

    it('zeigt «Kurs abschliessen» nur, wenn der Kurs abgeschlossen werden kann', async () => {
        render({ camp: { ...CAMP, can_finish: false } });
        await load();

        expect(edit().querySelector('[data-finish-section]').classList.contains('hidden')).toBe(true);
    });

    it('öffnet den Dialog beim Laden und zeigt nach dem Schliessen die Adresse der Übersicht', async () => {
        window.history.replaceState({}, '', '/admin/camps/7/edit');
        render({ camp: CAMP });
        await load();

        expect(edit().showModal).toHaveBeenCalledTimes(1);
        edit().querySelector('[data-dialog-close]').click();
        expect(window.location.pathname).toBe('/admin/camps');
    });

    it('lässt Ctrl-Klick auf den Kursnamen durch', async () => {
        render();
        const fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        await load();

        const event = new MouseEvent('click', { bubbles: true, cancelable: true, ctrlKey: true });
        document.querySelector('a[data-open-dialog]').dispatchEvent(event);

        expect(fetchMock).not.toHaveBeenCalled();
    });
});
