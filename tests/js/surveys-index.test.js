import { beforeEach, describe, expect, it, vi } from 'vitest';

const dialog = vi.hoisted(() => ({ confirmDialog: vi.fn(), messageDialog: vi.fn() }));
vi.mock('../../resources/js/lib/dialog.js', () => dialog);

function render() {
    document.head.innerHTML = '<meta name="csrf-token" content="token-123">';
    document.body.innerHTML = `
        <script type="application/json" id="surveys-index-config">
            {"createUrl": "/admin/surveys", "openSurveyUrl": "/admin/camps/opensurvey"}
        </script>
        <a href="javascript:;" class="create" role="button">Qualifikationen erstellen</a>
        <a href="javascript:;" class="opensurvey" role="button">Zweite Selbsteinschätzung freigeben</a>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/surveys-index.js');
}

let fetchMock;
let reload;

describe('Quali-Liste: Erstellen und Freigeben', () => {
    beforeEach(async () => {
        fetchMock = vi.fn(async () => ({ ok: true }));
        vi.stubGlobal('fetch', fetchMock);
        reload = vi.fn();
        vi.stubGlobal('location', { reload });
        render();
        await load();
    });

    it('erstellt die Qualifikationen erst nach der Rückfrage und lädt neu', async () => {
        dialog.confirmDialog.mockResolvedValue(true);

        document.querySelector('.create').click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(dialog.confirmDialog.mock.calls[0][0]).toBe('Qualifikationen erstellen?');
        expect(dialog.confirmDialog.mock.calls[0][1].confirmLabel).toBe('Qualifikationen erstellen');
        expect(fetchMock).toHaveBeenCalledWith('/admin/surveys', {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
    });

    it('tut bei "Abbrechen" nichts', async () => {
        dialog.confirmDialog.mockResolvedValue(false);

        document.querySelector('.create').click();
        await vi.waitFor(() => expect(dialog.confirmDialog).toHaveBeenCalled());

        expect(fetchMock).not.toHaveBeenCalled();
        expect(reload).not.toHaveBeenCalled();
    });

    it('gibt die Selbsteinschätzung ohne Rückfrage frei', async () => {
        document.querySelector('.opensurvey').click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(dialog.confirmDialog).not.toHaveBeenCalled();
        expect(fetchMock.mock.calls[0][0]).toBe('/admin/camps/opensurvey');
    });

    it('meldet einen Fehler statt neu zu laden', async () => {
        fetchMock.mockResolvedValue({ ok: false, status: 500 });

        document.querySelector('.opensurvey').click();
        await vi.waitFor(() => expect(dialog.messageDialog).toHaveBeenCalled());

        expect(dialog.messageDialog.mock.calls[0][0]).toBe('Das hat nicht geklappt.');
        expect(reload).not.toHaveBeenCalled();
    });
});
