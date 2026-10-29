import { describe, expect, it } from 'vitest';
import {
    compareValues,
    displayValue,
    filterByValue,
    filterRows,
    isSortable,
    parseSorting,
    serializeSorting,
    sortValue,
} from '../../resources/js/libs/table-data.js';

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'avatar', label: 'Bild', type: 'image' },
    { key: 'role', label: 'Rolle' },
    { key: 'last_login', label: 'Letztes Login' },
    { key: 'status', label: 'Status', type: 'status' },
    { key: 'actions', label: 'Aktionen', type: 'actions' },
    { key: 'note', label: 'Notiz', sortable: false },
];

describe('Zellwerte', () => {
    it('nimmt Anzeige- und Sortierwert aus { display, sort }, sonst den Wert selbst', () => {
        const date = { display: '05.03.2026', sort: '2026-03-05' };
        expect(displayValue(date)).toBe('05.03.2026');
        expect(sortValue(date)).toBe('2026-03-05');
        expect(displayValue(12)).toBe('12');
        expect(sortValue(12)).toBe(12);
        expect(displayValue(null)).toBe('');
        expect(sortValue(undefined)).toBe('');
    });

    it('sortiert nur Textspalten, die nicht ausgeschlossen sind', () => {
        expect(columns.filter(isSortable).map((column) => column.key)).toEqual(['name', 'role', 'last_login', 'status']);
    });
});

describe('Vergleich', () => {
    const sorted = (values) => [...values].sort(compareValues);

    it('vergleicht Zahlen als Zahlen', () => {
        expect(sorted([10, 9, 100])).toEqual([9, 10, 100]);
    });

    it('vergleicht Text natürlich, ohne Gross/Klein und mit Umlauten', () => {
        expect(sorted(['Kurs 10', 'kurs 9', 'Zora', 'Ärger', 'anna'])).toEqual(['anna', 'Ärger', 'kurs 9', 'Kurs 10', 'Zora']);
    });

    it('sortiert ISO-Daten richtig und leere Werte zuerst', () => {
        expect(sorted(['2026-03-05 18:07:00', '', '2025-12-31 08:00:00'])).toEqual([
            '',
            '2025-12-31 08:00:00',
            '2026-03-05 18:07:00',
        ]);
    });
});

describe('Suche', () => {
    const rows = [
        { id: 1, name: 'Anna Muster', role: 'Teilnehmer', last_login: { display: '05.03.2026', sort: '2026-03-05' }, avatar: '/img/anna.png' },
        { id: 2, name: 'Zora Beispiel', role: 'Gruppenleiter', last_login: '', avatar: '/img/zora.png' },
    ];

    it('findet alle Wörter in beliebigen Textspalten, ohne Gross/Klein', () => {
        expect(filterRows(rows, columns, 'anna teil').map((row) => row.id)).toEqual([1]);
        expect(filterRows(rows, columns, '05.03').map((row) => row.id)).toEqual([1]);
        expect(filterRows(rows, columns, '  ').map((row) => row.id)).toEqual([1, 2]);
    });

    it('durchsucht keine Bild-Adressen', () => {
        expect(filterRows(rows, columns, 'png')).toEqual([]);
    });
});

describe('Filter nach Wert', () => {
    const rows = [
        { id: 1, finish: 'Ja', end_date: { display: '05.03.2026', sort: '2026-03-05' } },
        { id: 2, finish: 'Nein', end_date: '' },
    ];

    it('behält nur Zeilen mit genau diesem Anzeigewert, ohne Wert alle', () => {
        expect(filterByValue(rows, 'finish', 'Nein').map((row) => row.id)).toEqual([2]);
        expect(filterByValue(rows, 'end_date', '05.03.2026').map((row) => row.id)).toEqual([1]);
        expect(filterByValue(rows, 'finish', null)).toBe(rows);
        expect(filterByValue(rows, undefined, 'Ja')).toBe(rows);
    });
});

describe('Sortierung in der Adresszeile', () => {
    it('liest und schreibt "spalte:richtung" mit Komma', () => {
        const sorting = [
            { id: 'role', desc: false },
            { id: 'name', desc: true },
        ];
        expect(serializeSorting(sorting)).toBe('role:asc,name:desc');
        expect(parseSorting('role:asc,name:desc', columns)).toEqual(sorting);
    });

    it('verwirft Ungültiges, auch das alte Format mit Spaltennummern', () => {
        expect(parseSorting('3:asc,4:asc', columns)).toBeNull();
        expect(parseSorting('avatar:asc', columns)).toBeNull();
        expect(parseSorting('name:up', columns)).toBeNull();
        expect(parseSorting('name:asc:x', columns)).toBeNull();
        expect(parseSorting('', columns)).toBeNull();
        expect(parseSorting(null, columns)).toBeNull();
    });

    it('nimmt jede Spalte nur einmal', () => {
        expect(parseSorting('name:asc,name:desc', columns)).toEqual([{ id: 'name', desc: false }]);
    });
});
