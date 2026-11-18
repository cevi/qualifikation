import { beforeAll, beforeEach, describe, expect, it, vi } from 'vitest';

const dialog = vi.hoisted(() => ({ confirmDialog: vi.fn() }));
vi.mock('../../resources/js/lib/dialog.js', () => dialog);

let confirmLabel;

beforeAll(async () => {
    ({ confirmLabel } = await import('../../resources/js/confirm-submit.js'));
});

describe('Absende-Knöpfe mit data-confirm', () => {
    let submit;

    beforeEach(() => {
        document.body.innerHTML = `
            <form>
                <button type="submit" name="action" value="close" data-confirm="Abschliessen?" data-confirm-text="Danach nicht mehr änderbar.">Speichern und abschliessen</button>
                <input type="submit" class="button-danger" value="Person löschen" data-confirm="Person löschen?">
                <button type="submit" title="Rückmeldung löschen" data-confirm="Rückmeldung löschen?"> <i class="fas fa-trash-alt"></i></button>
            </form>`;
        submit = vi.spyOn(HTMLFormElement.prototype, 'requestSubmit').mockImplementation(() => {});
    });

    it('fragt mit Text des Knopfs und schickt danach mit dem Knopf ab (name/value bleiben)', async () => {
        dialog.confirmDialog.mockResolvedValue(true);
        const button = document.querySelector('button[value="close"]');

        button.click();
        await vi.waitFor(() => expect(submit).toHaveBeenCalledWith(button));

        expect(dialog.confirmDialog).toHaveBeenCalledWith('Abschliessen?', {
            text: 'Danach nicht mehr änderbar.',
            confirmLabel: 'Speichern und abschliessen',
            danger: false,
        });
    });

    it('schickt nach Abbrechen nichts ab', async () => {
        dialog.confirmDialog.mockResolvedValue(false);

        document.querySelector('button[value="close"]').click();
        await vi.waitFor(() => expect(dialog.confirmDialog).toHaveBeenCalled());

        expect(submit).not.toHaveBeenCalled();
    });

    it('ist rot bei button-danger und bei «löschen»', async () => {
        dialog.confirmDialog.mockResolvedValue(false);

        document.querySelector('input').click();
        document.querySelector('button[title]').click();
        await vi.waitFor(() => expect(dialog.confirmDialog).toHaveBeenCalledTimes(2));

        expect(dialog.confirmDialog.mock.calls.map(([, options]) => [options.confirmLabel, options.danger])).toEqual([
            ['Person löschen', true],
            ['Rückmeldung löschen', true],
        ]);
    });

    it('nimmt Wert, Text oder Titel als Beschriftung', () => {
        expect(confirmLabel(document.querySelector('input'))).toBe('Person löschen');
        expect(confirmLabel(document.querySelector('button[title]'))).toBe('Rückmeldung löschen');
    });
});
