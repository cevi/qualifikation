import { beforeEach, describe, expect, it, vi } from 'vitest';

function render() {
    document.head.innerHTML = '<meta name="csrf-token" content="token-123">';
    document.body.innerHTML = `
        <a href="javascript:" class="ampel-btn" data-remote="/users/7/classifications/1">Grün</a>
        <a href="javascript:" class="ampel-btn" data-remote="/users/7/classifications/3">Rot</a>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/profile-ampel.js');
}

let fetchMock;
let reload;

describe('Ampel auf dem Profil', () => {
    beforeEach(async () => {
        fetchMock = vi.fn(async () => ({ ok: true }));
        vi.stubGlobal('fetch', fetchMock);
        reload = vi.fn();
        vi.stubGlobal('location', { reload });
        render();
        await load();
    });

    it('setzt die gewählte Farbe per PATCH und lädt neu', async () => {
        document.querySelectorAll('.ampel-btn')[1].click();
        await vi.waitFor(() => expect(reload).toHaveBeenCalled());

        expect(fetchMock).toHaveBeenCalledWith('/users/7/classifications/3', {
            method: 'PATCH',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
    });

    it('lädt bei einer Fehlerantwort nicht neu', async () => {
        fetchMock.mockResolvedValue({ ok: false, status: 403 });

        document.querySelector('.ampel-btn').click();
        await vi.waitFor(() => expect(fetchMock).toHaveBeenCalled());
        await Promise.resolve();

        expect(reload).not.toHaveBeenCalled();
    });
});
