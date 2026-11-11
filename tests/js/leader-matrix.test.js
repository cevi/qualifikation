import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import LeaderMatrix from '../../resources/js/components/LeaderMatrix.vue';
import { countByLeader, filterParticipants, withLeader } from '../../resources/js/lib/leader-assignment.js';

const leaders = [
    { id: 10, name: 'Anna' },
    { id: 11, name: 'Ben' },
];

const participants = [
    { id: 1, name: 'Eva', edit_url: '/admin/users/eva/edit', leader_ids: [10] },
    { id: 2, name: 'Finn', edit_url: '/admin/users/finn/edit', leader_ids: [10, 11] },
    { id: 3, name: 'Gina', edit_url: '/admin/users/gina/edit', leader_ids: [] },
];

describe('Zuteilungs-Logik', () => {
    it('zählt Teilnehmende pro Leitende/r, auch Leitende ohne Teilnehmende', () => {
        expect(countByLeader(participants, [...leaders, { id: 12, name: 'Carla' }])).toEqual({ 10: 2, 11: 1, 12: 0 });
    });

    it('sucht im Namen ohne Gross-/Kleinschreibung und filtert auf einen Schnappschuss', () => {
        expect(filterParticipants(participants, ' fi ').map((p) => p.name)).toEqual(['Finn']);
        expect(filterParticipants(participants, '', new Set([3])).map((p) => p.name)).toEqual(['Gina']);
    });

    it('liefert beim Zuteilen ein neues Array und ändert nur die betroffene Person', () => {
        const next = withLeader(participants, 3, 11, true);
        expect(next).not.toBe(participants);
        expect(next[2].leader_ids).toEqual([11]);
        expect(next[0]).toBe(participants[0]);
        expect(withLeader(next, 3, 11, true)[2].leader_ids).toEqual([11]);
        expect(withLeader(participants, 2, 10, false)[1].leader_ids).toEqual([11]);
    });
});

let fetchMock;

function stubScreen(desktop) {
    vi.stubGlobal('matchMedia', () => ({ matches: desktop, addEventListener: vi.fn(), removeEventListener: vi.fn() }));
}

function render() {
    return mount(LeaderMatrix, { props: { participants, leaders, url: '/admin/users/leaders' }, attachTo: document.body });
}

const checkbox = (wrapper, label) => wrapper.find(`input[aria-label="${label}"]`);
const headers = (wrapper) => wrapper.findAll('thead th').map((th) => th.text().replace(/\s+/g, ' '));
const names = (wrapper) => wrapper.findAll('tbody tr').map((row) => row.find('th a').text());

describe('Leitende zuteilen', () => {
    beforeEach(() => {
        fetchMock = vi.fn().mockResolvedValue({ ok: true, json: async () => ({ assigned: true }) });
        vi.stubGlobal('fetch', fetchMock);
        document.head.innerHTML = '<meta name="csrf-token" content="token-123">';
        stubScreen(true);
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    it('zeigt die Matrix mit Kreuzen, Zählern und Hinweis für Personen ohne Leitende', () => {
        const wrapper = render();

        expect(wrapper.find('[data-testid="matrix"]').exists()).toBe(true);
        expect(checkbox(wrapper, 'Anna leitet Eva').element.checked).toBe(true);
        expect(checkbox(wrapper, 'Ben leitet Eva').element.checked).toBe(false);
        expect(headers(wrapper)).toEqual(['Teilnehmende', 'Anna (2)', 'Ben (1)']);
        expect(wrapper.find('[data-testid="summary"]').text()).toBe('1 von 3 ohne Leitende');
        expect(wrapper.findAll('tbody tr')[2].text()).toContain('ohne Leitende');
    });

    it('speichert ein Kreuz sofort mit PUT und aktualisiert die Zähler', async () => {
        const wrapper = render();

        await checkbox(wrapper, 'Ben leitet Gina').setValue(true);
        await flushPromises();

        expect(fetchMock).toHaveBeenCalledWith('/admin/users/leaders/3/11', {
            method: 'PUT',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': 'token-123' },
        });
        expect(checkbox(wrapper, 'Ben leitet Gina').element.checked).toBe(true);
        expect(headers(wrapper)).toContain('Ben (2)');
        expect(wrapper.find('[data-testid="summary"]').text()).toBe('Alle 3 Teilnehmenden haben Leitende');
    });

    it('nimmt ein Kreuz mit DELETE weg', async () => {
        fetchMock.mockResolvedValue({ ok: true, json: async () => ({ assigned: false }) });
        const wrapper = render();

        await checkbox(wrapper, 'Anna leitet Eva').setValue(false);
        await flushPromises();

        expect(fetchMock.mock.calls[0][0]).toBe('/admin/users/leaders/1/10');
        expect(fetchMock.mock.calls[0][1].method).toBe('DELETE');
        expect(headers(wrapper)).toContain('Anna (1)');
    });

    it('sperrt das Kästchen während des Speicherns', async () => {
        let answer;
        fetchMock.mockReturnValue(new Promise((resolve) => (answer = resolve)));
        const wrapper = render();

        await checkbox(wrapper, 'Ben leitet Gina').setValue(true);
        expect(checkbox(wrapper, 'Ben leitet Gina').attributes('disabled')).toBeDefined();

        answer({ ok: true, json: async () => ({ assigned: true }) });
        await flushPromises();
        expect(checkbox(wrapper, 'Ben leitet Gina').attributes('disabled')).toBeUndefined();
    });

    it('setzt bei einem Fehler zurück und meldet ihn', async () => {
        fetchMock.mockResolvedValue({ ok: false, status: 500 });
        const wrapper = render();

        await checkbox(wrapper, 'Ben leitet Gina').setValue(true);
        await flushPromises();

        expect(checkbox(wrapper, 'Ben leitet Gina').element.checked).toBe(false);
        expect(wrapper.find('[role="alert"]').text()).toContain('Ben bei Gina wurde nicht gespeichert');
    });

    it('übernimmt den Stand vom Server (Demo speichert nichts)', async () => {
        fetchMock.mockResolvedValue({ ok: true, json: async () => ({ assigned: false }) });
        const wrapper = render();

        await checkbox(wrapper, 'Ben leitet Gina').setValue(true);
        await flushPromises();

        expect(checkbox(wrapper, 'Ben leitet Gina').element.checked).toBe(false);
    });

    it('"Nur ohne Leitende" behält eine Person nach dem ersten Kreuz in der Liste', async () => {
        const wrapper = render();

        await wrapper.find('input[type="checkbox"]:not([aria-label])').setValue(true);
        expect(names(wrapper)).toEqual(['Gina']);

        await checkbox(wrapper, 'Anna leitet Gina').setValue(true);
        await flushPromises();
        expect(names(wrapper)).toEqual(['Gina']);
        expect(wrapper.find('tbody tr').text()).not.toContain('ohne Leitende');
    });

    it('zeigt auf dem Handy Karten mit Knöpfen', async () => {
        stubScreen(false);
        const wrapper = render();

        expect(wrapper.find('[data-testid="matrix"]').exists()).toBe(false);
        const button = wrapper.find('[aria-label="Leitende von Gina"] button');
        expect(button.attributes('aria-pressed')).toBe('false');

        await button.trigger('click');
        await flushPromises();

        expect(fetchMock.mock.calls[0][0]).toBe('/admin/users/leaders/3/10');
        expect(wrapper.find('[aria-label="Leitende von Gina"] button').attributes('aria-pressed')).toBe('true');
    });

    it('erklärt, wenn es noch keine Leitenden gibt', () => {
        const wrapper = mount(LeaderMatrix, { props: { participants, leaders: [], url: '/x' } });
        expect(wrapper.text()).toContain('Im Kurs gibt es noch keine Leitenden');
    });
});
