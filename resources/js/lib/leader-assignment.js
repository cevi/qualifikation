/**
 * Zuteilung Leitende ↔ Teilnehmende (admin/users/leaders), ohne DOM.
 * Teilnehmende: { id (camp_user), name, edit_url, leader_ids: number[] }, Leitende: { id (user), name }.
 */

/** Anzahl Teilnehmende pro Leitende/r als { [leaderId]: Anzahl }. */
export function countByLeader(participants, leaders) {
    const counts = Object.fromEntries(leaders.map((leader) => [leader.id, 0]));
    participants.forEach((participant) =>
        participant.leader_ids.forEach((id) => {
            if (id in counts) {
                counts[id] += 1;
            }
        }),
    );
    return counts;
}

export function hasLeader(participant) {
    return participant.leader_ids.length > 0;
}

/**
 * Sichtbare Teilnehmende: Suche im Namen, optional nur die IDs in onlyIds. onlyIds ist ein Schnappschuss der Personen
 * ohne Leitende beim Einschalten des Filters, damit eine Zeile nach dem ersten Kreuz nicht verschwindet.
 */
export function filterParticipants(participants, search = '', onlyIds = null) {
    const needle = search.trim().toLocaleLowerCase('de-CH');
    return participants.filter(
        (participant) =>
            (!onlyIds || onlyIds.has(participant.id)) && (!needle || participant.name.toLocaleLowerCase('de-CH').includes(needle)),
    );
}

/** Neues Array (Vue erkennt die Änderung), bei der Person participantId ist leaderId (nicht) zugeteilt. */
export function withLeader(participants, participantId, leaderId, assigned) {
    return participants.map((participant) => {
        if (participant.id !== participantId) {
            return participant;
        }
        const others = participant.leader_ids.filter((id) => id !== leaderId);
        return { ...participant, leader_ids: assigned ? [...others, leaderId] : others };
    });
}

/** PUT teilt zu, DELETE nimmt weg; Antwort { assigned } ist der gespeicherte Stand. */
export async function saveAssignment(url, participantId, leaderId, assigned) {
    const response = await fetch(`${url}/${participantId}/${leaderId}`, {
        method: assigned ? 'PUT' : 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
    });
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    return Boolean((await response.json()).assigned);
}
