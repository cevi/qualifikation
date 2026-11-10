import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mit dem echten Dialog (resources/js/lib/dialog.js): "Importieren" ruft den Server, Fehler bleiben im Dialog.
const openDialog = () => document.querySelector('dialog[data-confirm-dialog]');

function render() {
    document.head.innerHTML = '<meta name="csrf-token" content="token-123">';
    document.body.innerHTML = '<button type="button" id="showImport" data-url="/admin/users/import">Importieren</button>';
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/users-import.js');
}

async function startAndConfirm() {
    document.getElementById('showImport').click();
    await vi.waitFor(() => expect(openDialog()).not.toBeNull());
    openDialog().querySelector('[data-dialog-confirm]').click();
}

let reload;

describe('Import aus der Cevi-DB', () => {
    beforeEach(async () => {
        render();
        reload = vi.spyOn(window.location, 'reload').mockImplementation(() => {});
        await load();
    });

    it('schickt den Import mit CSRF-Token an die Adresse aus data-url und lädt neu', async () => {
        const fetchMock = vi.fn().mockResolvedValue({ ok: true, json: async () => ({ skipped: [] }) });
        vi.stubGlobal('fetch', fetchMock);

        await startAndConfirm();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(fetchMock).toHaveBeenCalledWith('/admin/users/import', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
        expect(openDialog()).toBeNull();
    });

    it('tut bei "Abbrechen" nichts', async () => {
        const fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);

        document.getElementById('showImport').click();
        await vi.waitFor(() => expect(openDialog()).not.toBeNull());
        openDialog().querySelector('[data-dialog-cancel]').click();

        expect(fetchMock).not.toHaveBeenCalled();
        expect(reload).not.toHaveBeenCalled();
    });

    it('zeigt übersprungene Personen als Text, nicht als HTML, und lädt nach OK neu', async () => {
        const names = ['<img src=x onerror=alert(1)>', 'Tom & "Jerry"'];
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: true, json: async () => ({ skipped: names }) }));

        await startAndConfirm();
        await vi.waitFor(() => expect(openDialog()?.textContent).toContain('2 Person(en) wurden übersprungen'));

        expect([...openDialog().querySelectorAll('li')].map((li) => li.textContent)).toEqual(names);
        expect(openDialog().querySelector('img')).toBeNull();
        expect(reload).not.toHaveBeenCalled();

        openDialog().querySelector('[data-dialog-confirm]').click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());
    });

    it('zeigt die Fehlermeldung des Servers im Dialog und lädt nicht neu', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: false, json: async () => ({ error: 'Kein API-Token' }) }));

        await startAndConfirm();
        await vi.waitFor(() => expect(openDialog().querySelector('[role="alert"]').textContent).toBe('Kein API-Token'));

        expect(reload).not.toHaveBeenCalled();
    });
});
