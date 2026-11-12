import { beforeEach, describe, expect, it, vi } from 'vitest';
import { confirmDialog, messageDialog } from '../../resources/js/lib/dialog.js';

const openDialog = () => document.querySelector('dialog[data-confirm-dialog]');
const click = (selector) => openDialog().querySelector(selector).click();

describe('confirmDialog', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('zeigt Frage, Text und den Knopf mit der Aktion; Abbrechen hat den Fokus', () => {
        confirmDialog('Person löschen?', { text: 'Sicher?', confirmLabel: 'Person löschen' });

        expect(openDialog().open).toBe(true);
        expect(openDialog().textContent).toContain('Person löschen?');
        expect(openDialog().textContent).toContain('Sicher?');
        expect(openDialog().querySelector('[data-dialog-confirm]').textContent).toBe('Person löschen');
        expect(document.activeElement).toBe(openDialog().querySelector('[data-dialog-cancel]'));
    });

    it('liefert true nach Bestätigen und entfernt den Dialog', async () => {
        const result = confirmDialog('Weiter?');
        click('[data-dialog-confirm]');

        await expect(result).resolves.toBe(true);
        expect(openDialog()).toBeNull();
    });

    it('liefert false nach Abbrechen, Esc oder Klick daneben', async () => {
        let result = confirmDialog('Weiter?');
        click('[data-dialog-cancel]');
        await expect(result).resolves.toBe(false);

        result = confirmDialog('Weiter?');
        openDialog().dispatchEvent(new Event('cancel', { cancelable: true }));
        await expect(result).resolves.toBe(false);

        result = confirmDialog('Weiter?');
        openDialog().click();
        await expect(result).resolves.toBe(false);
    });

    it('ist rot mit Warnsymbol nur bei danger', () => {
        confirmDialog('Löschen?', { danger: true });
        expect(openDialog().querySelector('[data-dialog-confirm]').className).toContain('bg-red-700');
        click('[data-dialog-cancel]');

        confirmDialog('Erstellen?');
        expect(openDialog().querySelector('[data-dialog-confirm]').className).toContain('bg-blue-700');
    });

    it('zeigt Texte aus der Datenbank als Text, nicht als HTML', () => {
        confirmDialog('<img src=x onerror=alert(1)>?', { text: 'Tom & "Jerry"', items: ['<b>Zora</b>'] });

        expect(openDialog().querySelector('img, b')).toBeNull();
        expect(openDialog().querySelector('h2').textContent).toBe('<img src=x onerror=alert(1)>?');
        expect(openDialog().querySelector('li').textContent).toBe('<b>Zora</b>');
    });

    it('bleibt mit onConfirm offen, bis die Aktion fertig ist, und zeigt deren Fehler', async () => {
        const onConfirm = vi.fn().mockResolvedValueOnce('Keine Kurs-ID').mockResolvedValueOnce(undefined);
        const result = confirmDialog('Importieren?', { confirmLabel: 'Importieren', onConfirm });

        click('[data-dialog-confirm]');
        await vi.waitFor(() => expect(openDialog().querySelector('[role="alert"]').textContent).toBe('Keine Kurs-ID'));
        expect(openDialog().querySelector('[role="alert"]').classList.contains('hidden')).toBe(false);
        expect(openDialog().querySelector('[data-dialog-confirm]').disabled).toBe(false);

        click('[data-dialog-confirm]');
        await expect(result).resolves.toBe(true);
        expect(onConfirm).toHaveBeenCalledTimes(2);
    });

    it('meldet einen Fehler, wenn onConfirm wirft', async () => {
        confirmDialog('Importieren?', { onConfirm: async () => { throw new Error('offline'); } });

        click('[data-dialog-confirm]');

        await vi.waitFor(() => expect(openDialog().querySelector('[role="alert"]').textContent).toContain('nicht geklappt'));
    });
});

describe('messageDialog', () => {
    it('zeigt Titel, Text und Liste und schliesst mit OK', async () => {
        document.body.innerHTML = '';
        const done = messageDialog('Import abgeschlossen', { text: '2 übersprungen', items: ['Anna', 'Ben'], tone: 'notice' });

        expect([...openDialog().querySelectorAll('li')].map((li) => li.textContent)).toEqual(['Anna', 'Ben']);
        expect(document.activeElement).toBe(openDialog().querySelector('[data-dialog-confirm]'));
        click('[data-dialog-confirm]');

        await expect(done).resolves.toBeUndefined();
        expect(openDialog()).toBeNull();
    });
});
