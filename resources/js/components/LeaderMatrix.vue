<script setup>
/**
 * Leitende zuteilen (admin/users/leaders.blade.php): auf breiten Bildschirmen eine Matrix Teilnehmende × Leitende,
 * auf dem Handy pro Person eine Karte mit Knöpfen. Jeder Klick speichert sofort; schlägt das fehl, gilt wieder der
 * vorherige Stand und eine Meldung erscheint.
 */
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { countByLeader, filterParticipants, hasLeader, saveAssignment, withLeader } from '../lib/leader-assignment.js';

const props = defineProps({
    participants: { type: Array, required: true },
    leaders: { type: Array, required: true },
    url: { type: String, required: true },
});

const rows = ref(props.participants.map((participant) => ({ ...participant, leader_ids: [...participant.leader_ids] })));
const search = ref('');
const onlyUnassigned = ref(false);
const snapshot = ref(null);
const pending = ref(new Set());
const error = ref('');

watch(onlyUnassigned, (on) => {
    snapshot.value = on ? new Set(rows.value.filter((row) => !hasLeader(row)).map((row) => row.id)) : null;
});

const visibleRows = computed(() => filterParticipants(rows.value, search.value, snapshot.value));
const counts = computed(() => countByLeader(rows.value, props.leaders));
const unassignedCount = computed(() => rows.value.filter((row) => !hasLeader(row)).length);

const key = (row, leader) => `${row.id}-${leader.id}`;
const isAssigned = (row, leader) => row.leader_ids.includes(leader.id);
const isPending = (row, leader) => pending.value.has(key(row, leader));

function setPending(row, leader, on) {
    const next = new Set(pending.value);
    if (on) {
        next.add(key(row, leader));
    } else {
        next.delete(key(row, leader));
    }
    pending.value = next;
}

async function toggle(row, leader) {
    if (isPending(row, leader)) {
        return;
    }
    const assigned = !isAssigned(row, leader);
    rows.value = withLeader(rows.value, row.id, leader.id, assigned);
    setPending(row, leader, true);
    error.value = '';
    try {
        const saved = await saveAssignment(props.url, row.id, leader.id, assigned);
        rows.value = withLeader(rows.value, row.id, leader.id, saved);
    } catch {
        rows.value = withLeader(rows.value, row.id, leader.id, !assigned);
        error.value = `${leader.name} bei ${row.name} wurde nicht gespeichert. Bitte versuche es nochmals oder lade die Seite neu.`;
    } finally {
        setPending(row, leader, false);
    }
}

// Tailwind "md", wie in DataTable.vue: nur eine Darstellung zeichnen.
const desktopQuery = window.matchMedia('(min-width: 48rem)');
const isDesktop = ref(desktopQuery.matches);
const onMediaChange = (event) => {
    isDesktop.value = event.matches;
};
onMounted(() => desktopQuery.addEventListener('change', onMediaChange));
onBeforeUnmount(() => desktopQuery.removeEventListener('change', onMediaChange));
</script>

<template>
    <div class="space-y-3">
        <p v-if="leaders.length === 0" class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            Im Kurs gibt es noch keine Leitenden. Gib zuerst Personen die Rolle Gruppen-, Kurs- oder Stabsleitende.
        </p>
        <p v-else-if="rows.length === 0" class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            Im Kurs gibt es noch keine Teilnehmenden.
        </p>
        <template v-else>
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <label class="block md:w-72">
                    <span class="sr-only">Teilnehmende suchen</span>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Suchen …"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    />
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                    <input v-model="onlyUnassigned" type="checkbox" class="field-check" />
                    Nur ohne Leitende
                </label>
                <span class="text-sm md:ms-auto" :class="unassignedCount ? 'text-amber-700 dark:text-amber-400' : 'text-green-700 dark:text-green-400'" data-testid="summary">
                    {{ unassignedCount ? `${unassignedCount} von ${rows.length} ohne Leitende` : `Alle ${rows.length} Teilnehmenden haben Leitende` }}
                </span>
            </div>

            <div
                v-if="error"
                role="alert"
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400"
            >
                {{ error }}
            </div>

            <p v-if="visibleRows.length === 0" class="p-4 text-sm text-gray-600 dark:text-gray-400">Keine Teilnehmenden gefunden.</p>

            <div v-else-if="isDesktop" class="relative max-h-[75vh] overflow-auto rounded-lg border border-gray-200 shadow-xs dark:border-gray-700" data-testid="matrix">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <caption class="sr-only">Leitende pro Teilnehmende Person</caption>
                    <thead class="sticky top-0 z-20 bg-gray-50 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 uppercase dark:bg-gray-700">Teilnehmende</th>
                            <th v-for="leader in leaders" :key="leader.id" scope="col" class="px-3 py-3 text-center font-medium whitespace-nowrap">
                                {{ leader.name }}
                                <span class="block font-normal text-gray-500 dark:text-gray-400" :title="`${counts[leader.id]} Teilnehmende`">
                                    ({{ counts[leader.id] }})
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in visibleRows"
                            :key="row.id"
                            class="border-t border-gray-200 bg-white hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <th scope="row" class="sticky left-0 bg-inherit px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                <a :href="row.edit_url" class="hover:underline" title="Person bearbeiten">{{ row.name }}</a>
                                <span
                                    v-if="!row.leader_ids.length"
                                    class="ms-2 rounded-sm bg-amber-100 px-1.5 py-0.5 text-xs font-normal text-amber-800 dark:bg-amber-900 dark:text-amber-300"
                                    >ohne Leitende</span
                                >
                            </th>
                            <td v-for="leader in leaders" :key="leader.id" class="p-0 text-center">
                                <label class="flex h-full cursor-pointer items-center justify-center px-3 py-2">
                                    <input
                                        type="checkbox"
                                        class="field-check cursor-pointer disabled:cursor-wait"
                                        :checked="isAssigned(row, leader)"
                                        :disabled="isPending(row, leader)"
                                        :aria-label="`${leader.name} leitet ${row.name}`"
                                        @change="toggle(row, leader)"
                                    />
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <ul v-else class="space-y-3" data-testid="cards">
                <li
                    v-for="row in visibleRows"
                    :key="row.id"
                    class="rounded-lg border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <a :href="row.edit_url" class="font-medium text-gray-900 hover:underline dark:text-white">{{ row.name }}</a>
                        <span
                            v-if="!row.leader_ids.length"
                            class="rounded-sm bg-amber-100 px-1.5 py-0.5 text-xs text-amber-800 dark:bg-amber-900 dark:text-amber-300"
                            >ohne Leitende</span
                        >
                    </div>
                    <div class="flex flex-wrap gap-2" role="group" :aria-label="`Leitende von ${row.name}`">
                        <button
                            v-for="leader in leaders"
                            :key="leader.id"
                            type="button"
                            class="rounded-full border px-3 py-1.5 text-sm font-medium disabled:cursor-wait disabled:opacity-60"
                            :class="
                                isAssigned(row, leader)
                                    ? 'border-blue-700 bg-blue-700 text-white dark:border-blue-600 dark:bg-blue-600'
                                    : 'border-gray-300 bg-white text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600'
                            "
                            :aria-pressed="isAssigned(row, leader)"
                            :disabled="isPending(row, leader)"
                            @click="toggle(row, leader)"
                        >
                            {{ leader.name }}
                        </button>
                    </div>
                </li>
            </ul>
        </template>
    </div>
</template>
