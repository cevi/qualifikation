import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

const users = [
    { id: 7, username: 'Zora', email: 'zora@example.com' },
    { id: 8, username: 'Zoran', email: 'zoran@example.com' },
];

function render() {
    document.body.innerHTML = `
        <form>
            <input type="text" name="username" list="user-suggestions" data-search-url="/admin/users/searchajaxuser">
            <datalist id="user-suggestions"></datalist>
            <input type="hidden" name="user_id">
        </form>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/user-search.js');
}

function type(value) {
    const input = document.querySelector('input[name="username"]');
    input.value = value;
    input.dispatchEvent(new Event('input'));
}

const options = () => [...document.querySelectorAll('#user-suggestions option')].map((option) => option.value);
const userId = () => document.querySelector('input[name="user_id"]').value;

let fetchMock;

describe('Personensuche beim Hinzufügen', () => {
    beforeEach(async () => {
        vi.useFakeTimers();
        fetchMock = vi.fn(async () => ({ ok: true, json: async () => users }));
        vi.stubGlobal('fetch', fetchMock);
        render();
        await load();
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.unstubAllGlobals();
    });

    it('sucht erst ab drei Zeichen und nach einer kurzen Pause', async () => {
        type('Zo');
        await vi.runAllTimersAsync();
        expect(fetchMock).not.toHaveBeenCalled();

        type('Zor');
        type('Zora');
        await vi.runAllTimersAsync();
        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect(fetchMock.mock.calls[0][0]).toBe('/admin/users/searchajaxuser?term=Zora');
        expect(options()).toEqual(['Zora - zora@example.com', 'Zoran - zoran@example.com']);
    });

    it('übernimmt beim Wählen eines Vorschlags Name und ID', async () => {
        type('Zora');
        await vi.runAllTimersAsync();

        type('Zoran - zoran@example.com');
        expect(document.querySelector('input[name="username"]').value).toBe('Zoran');
        expect(userId()).toBe('8');
        expect(options()).toEqual([]);
    });

    it('vergisst die ID, wenn der Name danach geändert wird', async () => {
        type('Zora');
        await vi.runAllTimersAsync();
        type('Zora - zora@example.com');
        expect(userId()).toBe('7');

        type('Zorb');
        expect(userId()).toBe('');
    });

    it('zeigt keine veraltete Antwort, wenn inzwischen weniger getippt ist', async () => {
        let answer;
        fetchMock.mockImplementation(() => new Promise((resolve) => (answer = resolve)));
        type('Zora');
        await vi.runAllTimersAsync();
        type('Zo');
        answer({ ok: true, json: async () => users });
        await vi.runAllTimersAsync();

        expect(options()).toEqual([]);
    });

    it('füllt die Liste nach dem Wählen nicht mit einer späten Antwort wieder auf', async () => {
        type('Zora');
        await vi.runAllTimersAsync();
        let answer;
        fetchMock.mockImplementation(() => new Promise((resolve) => (answer = resolve)));
        type('Zoran');
        await vi.runAllTimersAsync();

        type('Zoran - zoran@example.com');
        answer({ ok: true, json: async () => users });
        await vi.runAllTimersAsync();

        expect(options()).toEqual([]);
        expect(userId()).toBe('8');
    });

    it('bleibt bei einem Netzwerkfehler ohne Vorschläge bedienbar', async () => {
        fetchMock.mockRejectedValue(new TypeError('Failed to fetch'));
        type('Zora');
        await vi.runAllTimersAsync();

        expect(options()).toEqual([]);
        expect(userId()).toBe('');
    });
});
