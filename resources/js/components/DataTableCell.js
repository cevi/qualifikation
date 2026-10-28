import { h } from 'vue';
import { displayValue } from '../libs/table-data.js';

/**
 * Eine Zelle der Admin-Tabellen, für Tabelle und Karte. Funktionale Komponente (keine Vue-Instanz pro Zelle).
 * Der Server liefert nur Text und URLs; Links, Bilder, Status und Knöpfe entstehen hier mit h(), Daten
 * werden nie als HTML gelesen (AdminTableEscapingTest).
 *
 * Spalte: { key, label, type?: 'image' | 'status' | 'actions', link?: Schlüssel der URL in der Zeile, linkTitle? }
 * Status: { display, sort, tone: gray | blue | orange | green, step, steps } (AdminSurveysController@tableData).
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

// Gleiche Klassen wie der Status-Badge der Übersicht (components/surveys/status-badge.blade.php)
const TONES = {
    gray: { badge: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', bar: 'bg-gray-400' },
    blue: { badge: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', bar: 'bg-blue-600 dark:bg-blue-500' },
    orange: { badge: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300', bar: 'bg-orange-500' },
    green: { badge: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', bar: 'bg-green-600 dark:bg-green-500' },
};

/** Status einer Quali: Badge mit dem Namen, daneben ein schmaler Balken "Schritt x von y". */
function status(value) {
    const tone = TONES[value.tone] ?? TONES.gray;
    const segments = Array.from({ length: value.steps }, (_, index) =>
        h('span', { class: ['h-1.5 w-4 rounded-full', index < value.step ? tone.bar : 'bg-gray-200 dark:bg-gray-600'] }),
    );
    return h('span', { class: 'inline-flex flex-wrap items-center gap-x-2 gap-y-1' }, [
        h('span', { class: ['rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap', tone.badge] }, value.display),
        h('span', { class: 'inline-flex items-center gap-1', 'aria-hidden': 'true', 'data-status-bar': '' }, [
            ...segments,
            h('span', { class: 'ms-1 text-xs text-gray-500 dark:text-gray-400' }, `${value.step}/${value.steps}`),
        ]),
        h('span', { class: 'sr-only' }, `, Schritt ${value.step} von ${value.steps}`),
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
        case 'status':
            return value ? status(value) : null;
        case 'actions':
            return Array.isArray(value) && value.length > 0 ? actions(value, block) : null;
        default:
            return link(row, column, displayValue(value));
    }
}

DataTableCell.props = ['column', 'row', 'block'];
