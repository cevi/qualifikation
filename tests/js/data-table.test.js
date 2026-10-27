import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import DataTable from '../../resources/js/components/DataTable.vue';

const columns = [
    { key: 'name', label: 'Name', link: 'edit_url', linkTitle: 'Person bearbeiten' },
    { key: 'avatar', label: 'Bild', type: 'image', link: 'profile_url' },
    { key: 'role', label: 'Rolle' },
    { key: 'counter', label: 'Anzahl' },
    { key: 'last_login', label: 'Letztes Login' },
    { key: 'steps', label: 'Status', type: 'steps' },
    { key: 'actions', label: 'Aktionen', type: 'actions' },
];

function person(id, name, role, counter, login) {
    return {
        id,
        name,
        edit_url: `/admin/users/${id}/edit`,
        profile_url: `/profile/${id}`,
        avatar: `/img/${id}.png`,
        role,
        counter,
        last_login: { display: login.split('-').reverse().join('.'), sort: login },
        steps: [
            { name: 'Neu', done: true },
            { name: '1. offen', done: id === 1 },
        ],
        actions: [{ label: 'Bearbeiten', url: `/surveys/${id}/edit` }],
    };
}

const rows = [
    person(1, 'Zora', 'Teilnehmer', 9, '2026-03-05'),
    person(2, 'anna', 'Gruppenleiter', 10, '2025-12-31'),
    person(3, 'Ben', 'Teilnehmer', 100, '2026-01-15'),
];

let fetchMock;

function respond(data) {
    fetchMock.mockResolvedValue({ ok: true, json: async () => ({ data }) });
}

async function render(props = {}) {
    const wrapper = mount(DataTable, { props: { url: '/admin/users/table', columns, ...props }, attachTo: document.body });
    await flushPromises();
    return wrapper;
}

const names = (wrapper) => wrapper.findAll('tbody tr').map((row) => row.find('td').text());

function stubPhone() {
    vi.stubGlobal('matchMedia', () => ({ matches: false, addEventListener: vi.fn(), removeEventListener: vi.fn() }));
}

describe('Admin-Tabelle', () => {
    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
        respond(rows);
        window.history.replaceState({}, '', '/admin/users');
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    it('zeigt beim Laden Platzhalter, danach die Zeilen', async () => {
        let answer;
        fetchMock.mockReturnValue(new Promise((resolve) => (answer = resolve)));
        const wrapper = mount(DataTable, { props: { url: '/admin/users/table', columns } });

        expect(wrapper.find('tbody').attributes('aria-busy')).toBe('true');
        expect(wrapper.findAll('tbody .animate-pulse').length).toBeGreaterThan(0);

        answer({ ok: true, json: async () => ({ data: rows }) });
        await flushPromises();
        expect(wrapper.find('tbody').attributes('aria-busy')).toBe('false');
        expect(names(wrapper)).toEqual(['Zora', 'anna', 'Ben']);
        expect(fetchMock).toHaveBeenCalledWith('/admin/users/table', { headers: { Accept: 'application/json' } });
    });

    it('baut Link, Bild, Status und Aktionen aus den Daten', async () => {
        const wrapper = await render();
        const cells = wrapper.find('tbody tr').findAll('td');

        expect(cells[0].find('a').attributes()).toMatchObject({ href: '/admin/users/1/edit', title: 'Person bearbeiten' });
        expect(cells[1].find('a').attributes('href')).toBe('/profile/1');
        expect(cells[1].find('img').attributes()).toMatchObject({ src: '/img/1.png', alt: '' });
        expect(cells[4].text()).toBe('05.03.2026');
        expect(cells[5].findAll('li').map((li) => li.classes().includes('active'))).toEqual([true, true]);
        expect(cells[5].findAll('li').map((li) => li.attributes('title'))).toEqual(['Neu', '1. offen']);
        expect(cells[6].find('a').attributes('href')).toBe('/surveys/1/edit');
    });

    it('zeigt Namen mit spitzen Klammern als Text', async () => {
        respond([person(1, '<img src=x onerror=alert(1)>', 'Teilnehmer', 1, '2026-01-01')]);
        const wrapper = await render();

        expect(wrapper.find('tbody td a').text()).toBe('<img src=x onerror=alert(1)>');
        expect(wrapper.findAll('tbody td:first-child img')).toHaveLength(0);
    });

    it('sortiert per Klick auf den Spaltenkopf, Zahlen als Zahlen und Daten nach dem ISO-Wert', async () => {
        const wrapper = await render();
        const header = (label) => wrapper.findAll('thead button').find((button) => button.text().startsWith(label));

        await header('Name').trigger('click');
        expect(names(wrapper)).toEqual(['anna', 'Ben', 'Zora']);
        expect(wrapper.findAll('thead th')[0].attributes('aria-sort')).toBe('ascending');

        await header('Name').trigger('click');
        expect(names(wrapper)).toEqual(['Zora', 'Ben', 'anna']);
        expect(wrapper.findAll('thead th')[0].attributes('aria-sort')).toBe('descending');

        await header('Anzahl').trigger('click');
        expect(names(wrapper)).toEqual(['Zora', 'anna', 'Ben']);

        await header('Letztes Login').trigger('click');
        expect(names(wrapper)).toEqual(['anna', 'Ben', 'Zora']);
    });

    it('sortiert mit Standardsortierung nach mehreren Spalten und Shift-Klick ergänzt eine Spalte', async () => {
        const wrapper = await render({ defaultSort: [{ id: 'role', desc: false }] });
        expect(names(wrapper)).toEqual(['anna', 'Zora', 'Ben']);

        const nameHeader = wrapper.findAll('thead button').find((button) => button.text().startsWith('Name'));
        await nameHeader.trigger('click', { shiftKey: true });
        expect(names(wrapper)).toEqual(['anna', 'Ben', 'Zora']);
        expect(nameHeader.text()).toContain('2');

        // Ohne Shift: nur noch nach Name, auch wenn Name schon Teil der Sortierung ist
        await nameHeader.trigger('click');
        expect(names(wrapper)).toEqual(['anna', 'Ben', 'Zora']);
        expect(wrapper.findAll('thead th')[2].attributes('aria-sort')).toBe('none');
        await nameHeader.trigger('click');
        expect(names(wrapper)).toEqual(['Zora', 'Ben', 'anna']);
    });

    it('sucht in allen Textspalten und zeigt, wie viele Zeilen passen', async () => {
        const wrapper = await render();

        await wrapper.find('input[type="search"]').setValue('teilnehmer');
        expect(names(wrapper)).toEqual(['Zora', 'Ben']);
        expect(wrapper.find('nav').text()).toContain('1–2 von 2 (gefiltert aus 3)');

        await wrapper.find('input[type="search"]').setValue('niemand');
        expect(wrapper.find('tbody').text()).toBe('Keine Einträge gefunden.');
    });

    it('blättert und springt bei einer neuen Suche zurück auf die erste Seite', async () => {
        const wrapper = await render({ pageSize: 2 });
        const next = () => wrapper.find('button[aria-label="Nächste Seite"]');

        expect(names(wrapper)).toEqual(['Zora', 'anna']);
        expect(wrapper.find('nav').text()).toContain('Seite 1 / 2');
        await next().trigger('click');
        expect(names(wrapper)).toEqual(['Ben']);
        expect(next().attributes('disabled')).toBeDefined();

        await wrapper.find('input[type="search"]').setValue('e');
        expect(wrapper.find('nav').text()).toContain('Seite 1 / 2');
    });

    it('meldet einen Ladefehler und lädt auf Wunsch erneut', async () => {
        fetchMock.mockResolvedValueOnce({ ok: false, status: 500 });
        const wrapper = await render();

        expect(wrapper.find('[role="alert"]').text()).toContain('Die Liste konnte nicht geladen werden.');
        await wrapper.find('[role="alert"] button').trigger('click');
        await flushPromises();
        expect(wrapper.find('[role="alert"]').exists()).toBe(false);
        expect(names(wrapper)).toEqual(['Zora', 'anna', 'Ben']);
    });

    describe('Sortierung in der Adresszeile', () => {
        const props = { sortParam: 'order', defaultSort: [{ id: 'role', desc: false }] };

        it('übernimmt die Sortierung aus der Adresse', async () => {
            window.history.replaceState({}, '', '/admin/users?order=name:desc');
            const wrapper = await render(props);

            expect(names(wrapper)).toEqual(['Zora', 'Ben', 'anna']);
        });

        it('nimmt bei ungültiger oder alter Adresse die Standardsortierung', async () => {
            window.history.replaceState({}, '', '/admin/users?order=3:asc');
            const wrapper = await render(props);

            expect(names(wrapper)).toEqual(['anna', 'Zora', 'Ben']);
        });

        it('schreibt neue Sortierungen in den Verlauf, die Zurück-Taste stellt sie wieder her', async () => {
            const pushState = vi.spyOn(window.history, 'pushState');
            const wrapper = await render(props);
            expect(pushState).not.toHaveBeenCalled();

            await wrapper.findAll('thead button')[0].trigger('click');
            expect(window.location.search).toBe('?order=name%3Aasc');
            expect(pushState).toHaveBeenCalledTimes(1);

            window.history.replaceState({}, '', '/admin/users');
            window.dispatchEvent(new PopStateEvent('popstate'));
            await flushPromises();
            expect(names(wrapper)).toEqual(['anna', 'Zora', 'Ben']);
            expect(pushState).toHaveBeenCalledTimes(1);
        });
    });

    describe('auf dem Handy', () => {
        const mobile = { title: 'name', image: 'avatar', kicker: 'role', lines: ['steps'] };

        it('zeigt Karten statt Tabelle mit Titel, Bild, Kopfzeile, Zeilen, Details und Aktionen', async () => {
            stubPhone();
            const wrapper = await render({ mobile });
            const card = wrapper.find('[data-testid="cards"] article');

            expect(wrapper.find('table').exists()).toBe(false);
            expect(wrapper.findAll('[data-testid="cards"] article')).toHaveLength(3);
            expect(card.find('[data-card="title"] a').text()).toBe('Zora');
            expect(card.find('[data-card="image"] img').attributes('src')).toBe('/img/1.png');
            expect(card.find('[data-card="kicker"]').text()).toBe('Teilnehmer');
            expect(card.find('[data-card="line"]').text()).toContain('Status:');
            expect(card.find('details dl').text()).toContain('Anzahl9');
            expect(card.find('details dl').text()).toContain('Letztes Login05.03.2026');
            expect(card.find('[data-card="actions"] a').attributes('href')).toBe('/surveys/1/edit');
        });

        it('nimmt ohne Angabe die erste Textspalte als Titel und die nächsten zwei als Zeilen', async () => {
            stubPhone();
            const wrapper = await render();
            const card = wrapper.find('[data-testid="cards"] article');

            expect(card.find('[data-card="title"]').text()).toBe('Zora');
            expect(card.findAll('[data-card="line"]').map((line) => line.text())).toEqual(['Rolle: Teilnehmer', 'Anzahl: 9']);
        });

        it('sortiert über das Auswahlfeld', async () => {
            stubPhone();
            const wrapper = await render({ mobile });

            await wrapper.find('select').setValue('counter:desc');
            expect(wrapper.findAll('[data-card="title"]').map((title) => title.text())).toEqual(['Ben', 'anna', 'Zora']);
        });
    });
});
