import { beforeEach, describe, expect, it, vi } from 'vitest';
import { initPhotoDialogs } from '../../resources/js/pages/photo-dialog.js';

// happy-dom kennt showModal/close nur teilweise: als Attrappe, die das open-Attribut setzt
function render() {
    document.body.innerHTML = `
        <button type="button" data-photo-open="foto">Foto</button>
        <dialog id="foto"><form method="dialog"><img alt="Foto"><button>Schliessen</button></form></dialog>`;
    const dialog = document.getElementById('foto');
    dialog.showModal = vi.fn(() => dialog.setAttribute('open', ''));
    dialog.close = vi.fn(() => dialog.removeAttribute('open'));
    initPhotoDialogs();
    return dialog;
}

describe('Foto vergrössern', () => {
    let dialog;

    beforeEach(() => {
        dialog = render();
    });

    it('öffnet den Dialog beim Klick auf das kleine Foto', () => {
        document.querySelector('[data-photo-open]').click();
        expect(dialog.showModal).toHaveBeenCalledTimes(1);
    });

    it('schliesst beim Klick neben das Bild, nicht beim Klick aufs Bild', () => {
        document.querySelector('[data-photo-open]').click();

        dialog.querySelector('img').click();
        expect(dialog.close).not.toHaveBeenCalled();

        dialog.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(dialog.close).toHaveBeenCalledTimes(1);
    });

    it('übergeht Knöpfe ohne passenden Dialog', () => {
        document.body.innerHTML = '<button data-photo-open="fehlt">Foto</button>';
        expect(() => initPhotoDialogs()).not.toThrow();
    });
});
