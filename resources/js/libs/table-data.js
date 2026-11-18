/**
 * Reine Funktionen für die Admin-Tabellen (components/DataTable.vue): Anzeige- und Sortierwert einer Zelle,
 * Vergleich, Suche und die Sortierung in der Adresszeile. Ein Zellwert ist Text, Zahl oder { display, sort }
 * (z. B. Datum: angezeigt "05.03.2026", sortiert "2026-03-05").
 */

// Spaltentypen ohne Text: nicht durchsuchbar und nicht sortierbar. "status" ist Text ({ display, sort }).
const NON_TEXT_TYPES = ['image', 'actions'];

export function isTextColumn(column) {
    return !NON_TEXT_TYPES.includes(column.type);
}

export function isSortable(column) {
    return isTextColumn(column) && column.sortable !== false;
}

export function displayValue(value) {
    if (value !== null && typeof value === 'object') {
        return String(value.display ?? '');
    }
    return String(value ?? '');
}

export function sortValue(value) {
    if (value !== null && typeof value === 'object') {
        return value.sort ?? '';
    }
    return value ?? '';
}

export function compareValues(a, b) {
    if (typeof a === 'number' && typeof b === 'number') {
        return a - b;
    }
    return String(a).localeCompare(String(b), 'de-CH', { numeric: true, sensitivity: 'base' });
}

/** Zeilen, in deren Textspalten alle Suchwörter vorkommen (ohne Gross/Klein). */
export function filterRows(rows, columns, term) {
    const words = term.trim().toLocaleLowerCase('de-CH').split(/\s+/).filter(Boolean);
    if (words.length === 0) {
        return rows;
    }
    const textColumns = columns.filter(isTextColumn);
    return rows.filter((row) => {
        const text = textColumns.map((column) => displayValue(row[column.key])).join(' ').toLocaleLowerCase('de-CH');
        return words.every((word) => text.includes(word));
    });
}

/** Zeilen, deren Spalte key genau diesen Anzeigewert hat; ohne Wert (null) alle. */
export function filterByValue(rows, key, value) {
    if (!key || value === null || value === undefined) {
        return rows;
    }
    return rows.filter((row) => displayValue(row[key]) === value);
}

/** "role:asc,name:desc" → [{ id: 'role', desc: false }, …]; null, wenn leer oder ungültig. */
export function parseSorting(param, columns) {
    if (!param) {
        return null;
    }
    const sortable = new Set(columns.filter(isSortable).map((column) => column.key));
    const sorting = [];
    for (const item of param.split(',').slice(0, 10)) {
        const [id, direction, ...rest] = item.split(':');
        if (rest.length > 0 || !sortable.has(id) || !['asc', 'desc'].includes(direction)) {
            return null;
        }
        if (!sorting.some((entry) => entry.id === id)) {
            sorting.push({ id, desc: direction === 'desc' });
        }
    }
    return sorting.length > 0 ? sorting : null;
}

export function serializeSorting(sorting) {
    return sorting.map(({ id, desc }) => `${id}:${desc ? 'desc' : 'asc'}`).join(',');
}
