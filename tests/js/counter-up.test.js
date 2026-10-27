import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

// Wie der Browser: der Rückruf kommt erst, wenn der Block ins Bild scrollt.
let observer;
class FakeIntersectionObserver {
    constructor(callback, options) {
        this.callback = callback;
        this.options = options;
        this.disconnect = vi.fn();
        observer = this;
    }

    observe(element) {
        this.element = element;
    }

    scrollIntoView(isIntersecting = true) {
        this.callback([{ target: this.element, isIntersecting }]);
    }
}

function render() {
    document.body.innerHTML = `
        <div id="counterUp">
            <h2 class="counter" data-counter="12">0</h2>
            <h2 class="counter" data-counter="347">0</h2>
        </div>`;
}

async function load() {
    vi.resetModules();
    await import('../../resources/js/pages/counter-up.js');
}

const values = () => [...document.querySelectorAll('.counter')].map((counter) => counter.textContent);

describe('Zähler auf der Startseite', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        observer = undefined;
        vi.stubGlobal('IntersectionObserver', FakeIntersectionObserver);
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.unstubAllGlobals();
    });

    it('zählt erst hoch, wenn die Zähler ins Bild kommen', async () => {
        render();
        await load();
        expect(observer.options.rootMargin).toBe('0px 0px -25% 0px');

        observer.scrollIntoView(false);
        await vi.runAllTimersAsync();
        expect(values()).toEqual(['0', '0']);

        observer.scrollIntoView();
        await vi.runAllTimersAsync();
        expect(values()).toEqual(['12', '347']);
        expect(observer.disconnect).toHaveBeenCalled();
    });

    it('zählt in Schritten bis zum Zielwert', async () => {
        render();
        await load();
        observer.scrollIntoView();

        expect(values()).toEqual(['2', '69']);
        await vi.runAllTimersAsync();
        expect(values()).toEqual(['12', '347']);
    });

    it('tut auf Seiten ohne Zähler nichts', async () => {
        document.body.innerHTML = '<p>Andere Seite</p>';
        await load();

        expect(observer).toBeUndefined();
    });
});
