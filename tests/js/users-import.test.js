import { beforeEach, describe, expect, it, vi } from 'vitest';

// Dasselbe Objekt für jeden Import, auch nach vi.resetModules().
const swal = vi.hoisted(() => ({ fire: vi.fn(), showValidationMessage: vi.fn(), isLoading: vi.fn() }));
vi.mock('sweetalert2', () => ({ default: swal }));

// Wie SweetAlert2: "Importieren" führt preConfirm aus; mit Fehlermeldung bleibt der Dialog offen.
function confirmDialogs() {
    swal.fire.mockImplementation(async (options) => {
        if (!options.preConfirm) {
            return { isConfirmed: true };
        }
        const value = await options.preConfirm();
        if (swal.showValidationMessage.mock.calls.length) {
            return new Promise(() => {});
        }

        return { isConfirmed: true, value };
    });
}

function render() {
    document.head.innerHTML = '<meta name="csrf-token" content="token-123">';
    document.body.innerHTML = '<button type="button" id="showImport" data-url="/admin/users/import">Importieren</button>';
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/users-import.js');
}

async function clickImport() {
    document.getElementById('showImport').click();
    await vi.waitFor(() => expect(reload).toHaveBeenCalled());
}

let reload;

describe('Import aus der Cevi-DB', () => {
    beforeEach(async () => {
        render();
        confirmDialogs();
        reload = vi.spyOn(window.location, 'reload').mockImplementation(() => {});
        await load();
    });

    it('schickt den Import mit CSRF-Token an die Adresse aus data-url und lädt neu', async () => {
        const fetchMock = vi.fn().mockResolvedValue({ ok: true, json: async () => ({ skipped: [] }) });
        vi.stubGlobal('fetch', fetchMock);

        await clickImport();

        expect(fetchMock).toHaveBeenCalledWith('/admin/users/import', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
        expect(swal.fire).toHaveBeenCalledTimes(1);
    });

    it('zeigt übersprungene Personen als Text, nicht als HTML', async () => {
        const names = ['<img src=x onerror=alert(1)>', 'Tom & "Jerry"'];
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: true, json: async () => ({ skipped: names }) }));

        await clickImport();

        const { html } = swal.fire.mock.calls[1][0];
        expect(html).toContain('2 Person(en) wurden übersprungen');
        expect(html).toContain('<li>&lt;img src=x onerror=alert(1)&gt;</li>');
        expect(html).toContain('<li>Tom &amp; &quot;Jerry&quot;</li>');
        expect(html).not.toContain('<img');
    });

    it('zeigt die Fehlermeldung des Servers im Dialog und lädt nicht neu', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: false, json: async () => ({ error: 'Kein API-Token' }) }));

        document.getElementById('showImport').click();
        await vi.waitFor(() => expect(swal.showValidationMessage).toHaveBeenCalledWith('Kein API-Token'));

        expect(reload).not.toHaveBeenCalled();
    });
});
