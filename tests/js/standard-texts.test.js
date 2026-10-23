import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

// Nachbau von home/post_delete.blade.php (Auswahl) und dem Kommentarfeld aus home/feedback_modal.blade.php.
function render() {
    document.body.innerHTML = `
        <textarea id="comment" name="comment"></textarea>
        <div id="default-modal" class="hidden">
            <button type="button" id="cancelBtn">x</button>
            <table id="optionTable">
                <thead><tr><th>Titel</th><th>Inhalt</th></tr></thead>
                <tbody>
                    <tr class="bg-white dark:bg-gray-800" data-value="Text A"><th>A</th><td>Text A</td></tr>
                    <tr class="bg-white dark:bg-gray-800" data-value="Text B"><th>B</th><td>Text B</td></tr>
                    <tr class="bg-white dark:bg-gray-800" data-value="Text C"><th>C</th><td>Text C</td></tr>
                </tbody>
            </table>
        </div>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/standard-texts.js');
}

const comment = () => document.getElementById('comment');
const modal = () => document.getElementById('default-modal');
const isOpen = () => modal().style.display === 'flex';
const highlighted = () => [...document.querySelectorAll('#optionTable tr[data-value]')]
    .filter((row) => row.classList.contains('bg-gray-50'))
    .map((row) => row.dataset.value);

// Tippen wie im Browser: Zeichen beim Cursor einfügen, Cursor dahinter, dann input-Event mit dem Zeichen.
function typeText(text) {
    const field = comment();
    for (const char of text) {
        const position = field.selectionStart;
        field.value = field.value.slice(0, position) + char + field.value.slice(position);
        field.setSelectionRange(position + 1, position + 1);
        field.dispatchEvent(new InputEvent('input', { data: char }));
    }
}

function placeCursor(position) {
    comment().focus();
    comment().setSelectionRange(position, position);
}

function press(key) {
    const event = new KeyboardEvent('keydown', { key, cancelable: true, bubbles: true });
    document.dispatchEvent(event);

    return event;
}

describe('Textbausteine mit //', () => {
    beforeEach(async () => {
        vi.useFakeTimers();
        render();
        await load();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('öffnet die Auswahl bei zwei schnellen / und entfernt sie aus dem Text', () => {
        typeText('Gut gemacht. //');

        expect(isOpen()).toBe(true);
        expect(comment().value).toBe('Gut gemacht. ');
        expect(highlighted()).toEqual(['Text A']);
    });

    it('lässt / mit Pause dazwischen stehen', () => {
        typeText('a/');
        vi.advanceTimersByTime(600);
        typeText('/');

        expect(isOpen()).toBe(false);
        expect(comment().value).toBe('a//');
    });

    it('wählt mit den Pfeiltasten und springt an den Enden um', () => {
        typeText('//');

        expect(press('ArrowDown').defaultPrevented).toBe(true);
        expect(highlighted()).toEqual(['Text B']);
        press('ArrowDown');
        press('ArrowDown');
        expect(highlighted()).toEqual(['Text A']);
        press('ArrowUp');
        expect(highlighted()).toEqual(['Text C']);
    });

    it('fügt mit Enter den gewählten Text ein und schliesst die Auswahl', () => {
        typeText('Fazit: //');
        press('ArrowDown');

        expect(press('Enter').defaultPrevented).toBe(true);
        expect(comment().value).toBe('Fazit: Text B');
        expect(isOpen()).toBe(false);
        expect(document.activeElement).toBe(comment());
    });

    it('zählt nach dem Einfügen ein neues / nicht als zweites eines //', () => {
        typeText('//');
        press('Enter');
        typeText('/');

        expect(isOpen()).toBe(false);
        expect(comment().value).toBe('Text A/');
    });

    it('öffnet nach dem Schliessen bei einem einzelnen / nicht erneut, auch wenn davor ein / steht', () => {
        typeText('km/');
        vi.advanceTimersByTime(600);
        typeText('//');
        expect(comment().value).toBe('km/');

        press('Escape');
        typeText('/');

        expect(isOpen()).toBe(false);
        expect(comment().value).toBe('km//');
    });

    it('entfernt // mitten im Text beim Cursor und fügt den Text dort ein', () => {
        typeText('Hallo Welt');
        placeCursor(6);
        typeText('//');

        expect(isOpen()).toBe(true);
        expect(comment().value).toBe('Hallo Welt');

        press('Enter');
        expect(comment().value).toBe('Hallo Text AWelt');
        expect(comment().selectionStart).toBe(12);
    });

    it('fügt per Klick auf eine Zeile ein', () => {
        typeText('//');
        document.querySelector('tr[data-value="Text C"]').click();

        expect(comment().value).toBe('Text C');
        expect(isOpen()).toBe(false);
    });

    it.each([
        ['Escape', () => press('Escape')],
        ['Kreuz', () => document.getElementById('cancelBtn').click()],
    ])('schliesst mit %s, ohne etwas einzufügen', (_, close) => {
        typeText('Text //');
        close();

        expect(isOpen()).toBe(false);
        expect(comment().value).toBe('Text ');
        expect(press('Enter').defaultPrevented).toBe(false);
        expect(comment().value).toBe('Text ');
    });

    it('lässt Tasten in Ruhe, solange die Auswahl zu ist', () => {
        typeText('Zeile');

        expect(press('Enter').defaultPrevented).toBe(false);
        expect(press('ArrowDown').defaultPrevented).toBe(false);
    });
});
