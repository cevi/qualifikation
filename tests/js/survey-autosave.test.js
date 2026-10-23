import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

// Nachbau von home/survey.blade.php: Form::model mit PATCH, Antworten als Radios, Kommentare als Textfelder.
function renderForms(count = 1) {
    document.body.innerHTML = Array.from({ length: count }, (_, i) => `
        <form method="POST" action="/survey/quali-${i}" class="survey-form">
            <input type="hidden" name="_method" value="PATCH">
            <input type="radio" name="answers[1]" value="2" id="q${i}-0" checked>
            <input type="radio" name="answers[1]" value="3" id="q${i}-plus">
            <textarea name="comments[1]"></textarea>
            <button type="submit" name="action" value="save">Quali speichern</button>
            <button type="submit" name="action" value="close">Quali speichern und abschliessen</button>
        </form>`).join('');

    return [...document.querySelectorAll('form')];
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/survey-autosave.js');
}

function choose(id) {
    const radio = document.getElementById(id);
    radio.checked = true;
    radio.dispatchEvent(new Event('change'));
}

function type(form, text) {
    const textarea = form.querySelector('textarea');
    textarea.value += text;
    textarea.dispatchEvent(new Event('input'));
}

const saveButton = (form) => form.querySelector('button[value="save"]');

// Antwort des Servers, die erst auf Kommando eintrifft.
function deferredResponse() {
    let resolve;
    const promise = new Promise((r) => { resolve = r; });
    return { promise, resolve };
}

describe('Quali automatisch speichern', () => {
    let fetchMock;

    beforeEach(() => {
        vi.useFakeTimers();
        fetchMock = vi.fn().mockResolvedValue({ ok: true, redirected: false });
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('speichert einmal, 1 s nach der letzten Änderung', async () => {
        const [form] = renderForms();
        await load();

        type(form, 'G');
        await vi.advanceTimersByTimeAsync(900);
        type(form, 'ut');
        choose('q0-plus');
        await vi.advanceTimersByTimeAsync(900);
        expect(fetchMock).not.toHaveBeenCalled();

        await vi.advanceTimersByTimeAsync(100);
        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it('schickt das ganze Formular als "speichern" an die Adresse des Formulars', async () => {
        const [form] = renderForms();
        await load();

        choose('q0-plus');
        type(form, 'Gut gemacht');
        await vi.advanceTimersByTimeAsync(1000);

        const [url, options] = fetchMock.mock.calls[0];
        expect(url).toBe('/survey/quali-0');
        expect(options.method).toBe('POST');
        expect(options.credentials).toBe('same-origin');
        expect(options.headers).toEqual({ 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' });
        expect(Object.fromEntries(options.body.entries())).toEqual({
            '_method': 'PATCH',
            'answers[1]': '3',
            'comments[1]': 'Gut gemacht',
            'action': 'save',
        });
    });

    it('zeigt den Zustand im Speichern-Knopf und stellt ihn nach 2 s zurück', async () => {
        const [form] = renderForms();
        const response = deferredResponse();
        fetchMock.mockReturnValue(response.promise);
        await load();

        choose('q0-plus');
        await vi.advanceTimersByTimeAsync(1000);
        expect(saveButton(form).textContent).toBe('Wird gespeichert...');

        response.resolve({ ok: true, redirected: false });
        await vi.advanceTimersByTimeAsync(0);
        expect(saveButton(form).textContent).toBe('Gespeichert ✓');

        await vi.advanceTimersByTimeAsync(2000);
        expect(saveButton(form).textContent).toBe('Quali speichern');
    });

    it.each([
        ['Server lehnt ab (z. B. 419 abgelaufene Sitzung)', () => Promise.resolve({ ok: false, redirected: false })],
        ['keine Verbindung', () => Promise.reject(new TypeError('Failed to fetch'))],
    ])('meldet einen Fehler: %s', async (_, respond) => {
        const [form] = renderForms();
        fetchMock.mockImplementation(respond);
        vi.spyOn(console, 'error').mockImplementation(() => {});
        await load();

        choose('q0-plus');
        await vi.advanceTimersByTimeAsync(1000);
        expect(saveButton(form).textContent).toBe('Fehler beim Speichern');

        await vi.advanceTimersByTimeAsync(3000);
        expect(saveButton(form).textContent).toBe('Quali speichern');
    });

    it('speichert bei mehreren Formularen nur das geänderte', async () => {
        const [first, second] = renderForms(2);
        await load();

        type(second, 'Nur hier');
        await vi.advanceTimersByTimeAsync(1000);

        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect(fetchMock.mock.calls[0][0]).toBe('/survey/quali-1');
        expect(saveButton(first).textContent).toBe('Quali speichern');
    });
});
