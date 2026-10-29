import { beforeEach, describe, expect, it, vi } from 'vitest';

// Das Modul legt die Tooltips beim Laden an; tippy hängt die Instanz ans Element (_tippy)
async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/tooltips.js');
}

const tooltipOf = (id) => document.getElementById(id)._tippy;

describe('Erklärungen auf Quali und Vergleich', () => {
    beforeEach(async () => {
        document.body.innerHTML = `
            <div id="kompetenz" data-tooltip="Kernkompetenz <b>fett</b>">1.1 Cevitechnik</div>
            <div id="leer" data-tooltip="  ">0</div>
            <div id="ohne">Ohne Erklärung</div>`;
        await load();
    });

    it('zeigt den Text erst beim Antippen bzw. Klick', () => {
        const instance = tooltipOf('kompetenz');
        expect(instance.state.isVisible).toBe(false);

        document.getElementById('kompetenz').dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(instance.state.isVisible).toBe(true);
        expect(instance.props).toMatchObject({ trigger: 'click', placement: 'top-start', theme: 'light-border' });
    });

    it('zeigt den Text als Text, nie als HTML', () => {
        const instance = tooltipOf('kompetenz');
        instance.show();

        const content = instance.popper.querySelector('.tippy-content');
        expect(content.textContent).toBe('Kernkompetenz <b>fett</b>');
        expect(content.querySelector('b')).toBeNull();
    });

    it('überspringt leere Erklärungen und Elemente ohne Erklärung', () => {
        expect(tooltipOf('leer')).toBeUndefined();
        expect(tooltipOf('ohne')).toBeUndefined();
    });
});
