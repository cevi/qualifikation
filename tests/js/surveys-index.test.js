import { beforeEach, describe, expect, it, vi } from 'vitest';

const swal = vi.hoisted(() => ({ fire: vi.fn() }));
vi.mock('sweetalert2', () => ({ default: swal }));

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
        swal.fire.mockResolvedValue({ isConfirmed: true });

        document.querySelector('.create').click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(swal.fire.mock.calls[0][0].titleText).toBe('Qualifikationen erstellen?');
        expect(fetchMock).toHaveBeenCalledWith('/admin/surveys', {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
    });

    it('tut bei "Abbrechen" nichts', async () => {
        swal.fire.mockResolvedValue({ isConfirmed: false });

        document.querySelector('.create').click();
        await vi.waitFor(() => expect(swal.fire).toHaveBeenCalled());

        expect(fetchMock).not.toHaveBeenCalled();
        expect(reload).not.toHaveBeenCalled();
    });

    it('gibt die Selbsteinschätzung ohne Rückfrage frei', async () => {
        document.querySelector('.opensurvey').click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(swal.fire).not.toHaveBeenCalled();
        expect(fetchMock.mock.calls[0][0]).toBe('/admin/camps/opensurvey');
    });

    it('meldet einen Fehler statt neu zu laden', async () => {
        fetchMock.mockResolvedValue({ ok: false, status: 500 });

        document.querySelector('.opensurvey').click();
        await vi.waitFor(() => expect(swal.fire).toHaveBeenCalled());

        expect(swal.fire.mock.calls[0][0]).toMatchObject({ icon: 'error', titleText: 'Das hat nicht geklappt.' });
        expect(reload).not.toHaveBeenCalled();
    });
});
