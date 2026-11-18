import { beforeEach, describe, expect, it, vi } from 'vitest';

// Markup wie components/layouts/toggle-switch.blade.php nach dem Inline-Skript (Symbol schon gesetzt).
function render(dark) {
    document.documentElement.classList.toggle('dark', dark);
    document.body.innerHTML = `
        <button id="theme-toggle" type="button">
            <svg id="theme-toggle-dark-icon" class="${dark ? 'hidden ' : ''}w-5 h-5"></svg>
            <svg id="theme-toggle-light-icon" class="${dark ? '' : 'hidden '}w-5 h-5"></svg>
        </button>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/theme-toggle.js');
}

const click = () => document.getElementById('theme-toggle').click();
const isHidden = (id) => document.getElementById(id).classList.contains('hidden');

describe('Hell/Dunkel-Umschalter', () => {
    beforeEach(() => localStorage.clear());

    it('wechselt von hell zu dunkel und merkt sich die Wahl', async () => {
        render(false);
        await load();

        click();
        expect(document.documentElement.classList.contains('dark')).toBe(true);
        expect(localStorage.getItem('color-theme')).toBe('dark');
        expect(isHidden('theme-toggle-light-icon')).toBe(false);
        expect(isHidden('theme-toggle-dark-icon')).toBe(true);
    });

    it('wechselt von dunkel (Systemeinstellung, nichts gespeichert) zu hell', async () => {
        render(true);
        await load();

        click();
        expect(document.documentElement.classList.contains('dark')).toBe(false);
        expect(localStorage.getItem('color-theme')).toBe('light');
        expect(isHidden('theme-toggle-dark-icon')).toBe(false);
        expect(isHidden('theme-toggle-light-icon')).toBe(true);
    });

    it('schaltet bei zwei Klicks wieder zurück', async () => {
        render(false);
        await load();

        click();
        click();
        expect(document.documentElement.classList.contains('dark')).toBe(false);
        expect(localStorage.getItem('color-theme')).toBe('light');
    });

    it('tut auf Seiten ohne Menü nichts', async () => {
        document.body.innerHTML = '<p>PDF-Ansicht</p>';
        await expect(load()).resolves.toBeUndefined();
    });
});
