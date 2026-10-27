import { h } from 'vue';
import { displayValue } from '../libs/table-data.js';

/**
 * Eine Zelle der Admin-Tabellen, für Tabelle und Karte. Funktionale Komponente (keine Vue-Instanz pro Zelle).
 * Der Server liefert nur Text und URLs; Links, Bilder, Status-Schritte und Knöpfe entstehen hier mit h(), Daten
 * werden nie als HTML gelesen (AdminTableEscapingTest).
 *
 * Spalte: { key, label, type?: 'image' | 'steps' | 'actions', link?: Schlüssel der URL in der Zeile, linkTitle? }
 */
export const LINK_CLASS = 'font-medium text-blue-600 dark:text-blue-500 hover:underline';
const BUTTON_CLASS =
    'inline-block text-center focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900';

function link(row, column, content) {
    const href = column.link ? row[column.link] : null;
    if (!href) {
        return content;
    }
    return h('a', { href, title: column.linkTitle, class: column.type === 'image' ? undefined : LINK_CLASS }, content);
}

function steps(value) {
    return h('div', { class: 'card card-progress' }, [
        h(
            'ul',
            { class: 'progressbar text-center' },
            value.map((step) => h('li', { class: [step.done ? 'active' : '', 'step0'], title: step.name })),
        ),
        // Für Screenreader: die Kreise selbst sind leer.
        h('span', { class: 'sr-only' }, value.filter((step) => step.done).map((step) => step.name).at(-1) ?? ''),
    ]);
}

function actions(value, block) {
    return h(
        'div',
        { class: ['flex gap-2', block ? 'flex-col' : 'flex-col items-start'] },
        value.map((action) =>
            h(
                'a',
                {
                    href: action.url,
                    // Auf der Karte über die ganze Breite, in der Tabelle ohne Umbruch
                    class: [action.button ? BUTTON_CLASS : LINK_CLASS, block && action.button ? 'w-full' : 'whitespace-nowrap'],
                    role: action.button ? 'button' : undefined,
                    target: action.newTab ? '_blank' : undefined,
                    rel: action.newTab ? 'noopener' : undefined,
                },
                action.label,
            ),
        ),
    );
}

export default function DataTableCell({ column, row, block = false }) {
    const value = row[column.key];
    switch (column.type) {
        case 'image':
            return value ? link(row, column, h('img', { class: 'img-user rounded-full', alt: '', src: value })) : null;
        case 'steps':
            return Array.isArray(value) ? steps(value) : null;
        case 'actions':
            return Array.isArray(value) && value.length > 0 ? actions(value, block) : null;
        default:
            return link(row, column, displayValue(value));
    }
}

DataTableCell.props = ['column', 'row', 'block'];
