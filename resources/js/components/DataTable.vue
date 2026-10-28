<script setup>
/**
 * Admin-Tabelle (Personen, Kurse, Qualifikationen): lädt alle Zeilen einmal als JSON, sucht, sortiert und blättert
 * im Browser. Ab Tailwind "md" eine Tabelle, darunter eine Karte pro Zeile; gezeichnet wird nur eine der beiden.
 *
 * Props aus Blade (data-props), Spalten siehe DataTableCell.js. mobile: { title, image, kicker, lines, details }
 * mit Spaltenschlüsseln; ohne Angabe erste Textspalte als Titel, die nächsten zwei als Zeilen, Rest unter "Mehr".
 * filter: { key, label, options: [{ label, value }], default } zeigt Knöpfe, die nach dem Anzeigewert einer Spalte
 * filtern (value null = alle).
 */
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { getCoreRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table';
import DataTableCell from './DataTableCell.js';
import {
    compareValues,
    displayValue,
    filterByValue,
    filterRows,
    isSortable,
    isTextColumn,
    parseSorting,
    serializeSorting,
    sortValue,
} from '../libs/table-data.js';

const props = defineProps({
    url: { type: String, required: true },
    columns: { type: Array, required: true },
    // [{ id: Spaltenschlüssel, desc: false }, …]
    defaultSort: { type: Array, default: () => [] },
    // Name des Parameters, wenn die Sortierung in der Adresszeile stehen soll (Link teilen, Zurück-Taste)
    sortParam: { type: String, default: null },
    pageSize: { type: Number, default: 25 },
    mobile: { type: Object, default: null },
    label: { type: String, default: 'Tabelle' },
    filter: { type: Object, default: null },
});

const rows = ref([]);
const status = ref('loading');
const search = ref('');
const filterValue = ref(props.filter?.default ?? null);
const filteredRows = computed(() =>
    filterRows(filterByValue(rows.value, props.filter?.key, filterValue.value), props.columns, search.value),
);

function sortingFromUrl() {
    if (!props.sortParam) {
        return props.defaultSort;
    }
    const param = new URLSearchParams(window.location.search).get(props.sortParam);
    return parseSorting(param, props.columns) ?? props.defaultSort;
}

const sorting = ref(sortingFromUrl());
const pagination = ref({ pageIndex: 0, pageSize: props.pageSize });

const table = useVueTable({
    data: filteredRows,
    columns: props.columns.map((column) => ({
        id: column.key,
        accessorFn: (row) => sortValue(row[column.key]),
        header: column.label,
        enableSorting: isSortable(column),
        sortDescFirst: false,
        sortingFn: (a, b, id) => compareValues(a.getValue(id), b.getValue(id)),
        meta: { column },
    })),
    state: {
        get sorting() {
            return sorting.value;
        },
        get pagination() {
            return pagination.value;
        },
    },
    onSortingChange: (updater) => {
        sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater;
    },
    onPaginationChange: (updater) => {
        pagination.value = typeof updater === 'function' ? updater(pagination.value) : updater;
    },
    autoResetPageIndex: false,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
});

watch([search, filterValue], () => {
    pagination.value = { ...pagination.value, pageIndex: 0 };
});

// Sortierung in die Adresszeile; nicht beim ersten Laden und nicht, wenn sie schon dort steht (Zurück-Taste).
watch(sorting, (value) => {
    if (!props.sortParam) {
        return;
    }
    const url = new URL(window.location.href);
    const current = url.searchParams.get(props.sortParam) ?? serializeSorting(props.defaultSort);
    const next = serializeSorting(value);
    if (current === next) {
        return;
    }
    if (next) {
        url.searchParams.set(props.sortParam, next);
    } else {
        url.searchParams.delete(props.sortParam);
    }
    window.history.pushState({}, '', url);
});

function onPopState() {
    sorting.value = sortingFromUrl();
}

async function load() {
    status.value = 'loading';
    try {
        const response = await fetch(props.url, { headers: { Accept: 'application/json' } });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        rows.value = (await response.json()).data;
        status.value = 'ready';
    } catch {
        status.value = 'error';
    }
}

// Tailwind "md"
const desktopQuery = window.matchMedia('(min-width: 48rem)');
const isDesktop = ref(desktopQuery.matches);
const onMediaChange = (event) => {
    isDesktop.value = event.matches;
};

onMounted(() => {
    desktopQuery.addEventListener('change', onMediaChange);
    window.addEventListener('popstate', onPopState);
    load();
});

onBeforeUnmount(() => {
    desktopQuery.removeEventListener('change', onMediaChange);
    window.removeEventListener('popstate', onPopState);
});

const visibleRows = computed(() => table.getRowModel().rows);
const total = computed(() => filteredRows.value.length);
const pageCount = computed(() => Math.max(table.getPageCount(), 1));
const range = computed(() => {
    const { pageIndex, pageSize } = pagination.value;
    const from = total.value === 0 ? 0 : pageIndex * pageSize + 1;
    return { from, to: Math.min((pageIndex + 1) * pageSize, total.value) };
});

// Klick: nur nach dieser Spalte (war sie schon die einzige, Richtung drehen); Shift-Klick: Spalte ergänzen bzw.
// drehen. TanStack würde bei der letzten Spalte einer Mehrfachsortierung nur deren Richtung drehen.
function sortBy(column, event) {
    if (event.shiftKey) {
        column.toggleSorting(undefined, true);
        return;
    }
    const only = sorting.value.length === 1 && sorting.value[0].id === column.id;
    sorting.value = [{ id: column.id, desc: only ? !sorting.value[0].desc : false }];
}

function ariaSort(column) {
    const direction = column.getIsSorted();
    if (!direction) {
        return column.getCanSort() ? 'none' : undefined;
    }
    return direction === 'asc' ? 'ascending' : 'descending';
}

function sortIcon(column) {
    const direction = column.getIsSorted();
    return direction === 'asc' ? '▲' : direction === 'desc' ? '▼' : '↕';
}

// Karten
const byKey = computed(() => Object.fromEntries(props.columns.map((column) => [column.key, column])));
const card = computed(() => {
    const mobile = props.mobile ?? {};
    const textColumns = props.columns.filter(isTextColumn);
    const pick = (keys) => keys.map((key) => byKey.value[key]).filter(Boolean);
    const title = byKey.value[mobile.title] ?? textColumns[0];
    const image = byKey.value[mobile.image] ?? null;
    const kicker = byKey.value[mobile.kicker] ?? null;
    const lines = pick(mobile.lines ?? textColumns.slice(1, 3).map((column) => column.key));
    const used = new Set([title, image, kicker, ...lines].filter(Boolean).map((column) => column.key));
    const details = pick(
        mobile.details ??
            props.columns.filter((column) => !used.has(column.key) && column.type !== 'actions').map((column) => column.key),
    );
    const actions = props.columns.find((column) => column.type === 'actions') ?? null;
    return { title, image, kicker, lines, details, actions };
});

function hasValue(row, column) {
    const value = row[column.key];
    if (Array.isArray(value)) {
        return value.length > 0;
    }
    return column.type === 'image' ? Boolean(value) : displayValue(value) !== '';
}

const sortOptions = computed(() =>
    props.columns.filter(isSortable).flatMap((column) => [
        { value: `${column.key}:asc`, label: `${column.label} ↑` },
        { value: `${column.key}:desc`, label: `${column.label} ↓` },
    ]),
);
const mobileSort = computed({
    get: () => serializeSorting(sorting.value.slice(0, 1)),
    set: (value) => {
        sorting.value = parseSorting(value, props.columns) ?? [];
    },
});
</script>

<template>
    <div class="space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <label class="block md:w-72">
                <span class="sr-only">Suchen</span>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Suchen …"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                />
            </label>
            <div v-if="filter" class="flex md:inline-flex" role="group" :aria-label="filter.label">
                <button
                    v-for="(option, index) in filter.options"
                    :key="option.label"
                    type="button"
                    class="flex-1 border px-4 py-2.5 text-sm font-medium focus:z-10 focus:ring-2 focus:ring-blue-300 focus:outline-hidden dark:focus:ring-blue-800"
                    :class="[
                        index === 0 ? 'rounded-s-lg' : '-ms-px',
                        index === filter.options.length - 1 ? 'rounded-e-lg' : '',
                        (option.value ?? null) === filterValue
                            ? 'z-10 border-blue-700 bg-blue-700 text-white dark:border-blue-600 dark:bg-blue-600'
                            : 'border-gray-300 bg-white text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600',
                    ]"
                    :aria-pressed="(option.value ?? null) === filterValue"
                    @click="filterValue = option.value ?? null"
                >
                    {{ option.label }}
                </button>
            </div>
            <label v-if="!isDesktop" class="block">
                <span class="sr-only">Sortieren nach</span>
                <select
                    v-model="mobileSort"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">Sortieren nach …</option>
                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
            </label>
        </div>

        <div
            v-if="status === 'error'"
            role="alert"
            class="flex flex-wrap items-center gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400"
        >
            Die Liste konnte nicht geladen werden.
            <button type="button" class="font-medium underline" @click="load">Erneut laden</button>
        </div>

        <div
            v-else-if="isDesktop"
            class="relative overflow-x-auto rounded-lg border border-gray-200 shadow-xs dark:border-gray-700"
            data-testid="table"
        >
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <caption class="sr-only">{{ label }}</caption>
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <th
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            scope="col"
                            class="px-4 py-3 whitespace-nowrap"
                            :aria-sort="ariaSort(header.column)"
                        >
                            <button
                                v-if="header.column.getCanSort()"
                                type="button"
                                class="inline-flex items-center gap-1 uppercase hover:text-gray-900 dark:hover:text-white"
                                title="Sortieren, mit Shift nach mehreren Spalten"
                                @click="sortBy(header.column, $event)"
                            >
                                {{ header.column.columnDef.header }}
                                <span aria-hidden="true" :class="header.column.getIsSorted() ? '' : 'text-gray-400'">{{ sortIcon(header.column) }}</span>
                                <span v-if="sorting.length > 1 && header.column.getSortIndex() >= 0" class="text-[0.65rem] text-gray-500">
                                    {{ header.column.getSortIndex() + 1 }}
                                </span>
                            </button>
                            <template v-else>{{ header.column.columnDef.header }}</template>
                        </th>
                    </tr>
                </thead>
                <tbody :aria-busy="status === 'loading'">
                    <template v-if="status === 'loading'">
                        <tr v-for="n in 5" :key="n" class="border-t border-gray-200 dark:border-gray-700">
                            <td v-for="column in columns" :key="column.key" class="px-4 py-3">
                                <div class="h-3 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div>
                            </td>
                        </tr>
                    </template>
                    <tr v-else-if="visibleRows.length === 0" class="border-t border-gray-200 dark:border-gray-700">
                        <td :colspan="columns.length" class="px-4 py-6 text-center">Keine Einträge gefunden.</td>
                    </tr>
                    <template v-else>
                        <tr
                            v-for="row in visibleRows"
                            :key="row.original.id"
                            class="border-t border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-3">
                                <DataTableCell :column="cell.column.columnDef.meta.column" :row="row.original" />
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div v-else class="space-y-3" data-testid="cards" :aria-busy="status === 'loading'">
            <template v-if="status === 'loading'">
                <div v-for="n in 3" :key="n" class="h-24 animate-pulse rounded-lg bg-gray-200 dark:bg-gray-700"></div>
            </template>
            <p v-else-if="visibleRows.length === 0" class="rounded-lg bg-white p-4 text-center text-sm dark:bg-gray-800">
                Keine Einträge gefunden.
            </p>
            <template v-else>
            <article
                v-for="row in visibleRows"
                :key="row.original.id"
                class="rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-600 shadow-xs dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
            >
                <div class="flex items-start gap-3">
                    <div v-if="card.image && hasValue(row.original, card.image)" class="shrink-0" data-card="image">
                        <DataTableCell :column="card.image" :row="row.original" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p v-if="card.kicker && hasValue(row.original, card.kicker)" class="text-xs text-gray-500 dark:text-gray-400" data-card="kicker">
                            <DataTableCell :column="card.kicker" :row="row.original" />
                        </p>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white" data-card="title">
                            <DataTableCell :column="card.title" :row="row.original" />
                        </h3>
                        <template v-for="column in card.lines" :key="column.key">
                            <div v-if="hasValue(row.original, column)" class="mt-1" data-card="line">
                                <span class="text-gray-500 dark:text-gray-400">{{ column.label }}:</span>{{ ' ' }}<DataTableCell :column="column" :row="row.original" />
                            </div>
                        </template>
                    </div>
                </div>
                <details v-if="card.details.some((column) => hasValue(row.original, column))" class="mt-2">
                    <summary class="cursor-pointer text-blue-600 dark:text-blue-500">Mehr</summary>
                    <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-3 gap-y-1">
                        <template v-for="column in card.details" :key="column.key">
                            <template v-if="hasValue(row.original, column)">
                                <dt class="text-gray-500 dark:text-gray-400">{{ column.label }}</dt>
                                <dd class="min-w-0 wrap-break-word"><DataTableCell :column="column" :row="row.original" /></dd>
                            </template>
                        </template>
                    </dl>
                </details>
                <div v-if="card.actions && hasValue(row.original, card.actions)" class="mt-3" data-card="actions">
                    <DataTableCell :column="card.actions" :row="row.original" :block="true" />
                </div>
            </article>
            </template>
        </div>

        <nav
            v-if="status === 'ready'"
            class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-600 dark:text-gray-300"
            aria-label="Seiten"
        >
            <p aria-live="polite">
                {{ range.from }}–{{ range.to }} von {{ total }}
                <span v-if="total !== rows.length">(gefiltert aus {{ rows.length }})</span>
            </p>
            <div v-if="pageCount > 1" class="flex items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 hover:bg-gray-100 disabled:opacity-40 dark:border-gray-600 dark:hover:bg-gray-700"
                    :disabled="!table.getCanPreviousPage()"
                    aria-label="Vorherige Seite"
                    @click="table.previousPage()"
                >
                    ←
                </button>
                <span>Seite {{ pagination.pageIndex + 1 }} / {{ pageCount }}</span>
                <button
                    type="button"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 hover:bg-gray-100 disabled:opacity-40 dark:border-gray-600 dark:hover:bg-gray-700"
                    :disabled="!table.getCanNextPage()"
                    aria-label="Nächste Seite"
                    @click="table.nextPage()"
                >
                    →
                </button>
            </div>
        </nav>
    </div>
</template>
