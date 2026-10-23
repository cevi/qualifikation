/**
 * Text für HTML-Vorlagen (z. B. Swal.fire({ html })) maskieren. Für Werte, die Personen eingeben
 * oder die aus anderen Systemen kommen (Namen aus der Cevi-DB).
 */
const ENTITIES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

export function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, (c) => ENTITIES[c]);
}
