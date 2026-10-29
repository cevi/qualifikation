/**
 * Service Worker registrieren (public/sw.js): App installierbar machen und ohne Netz eine Offline-Seite zeigen.
 * Seiten selbst werden nicht gespeichert (Personendaten).
 */
if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Ohne Service Worker funktioniert das Tool wie bisher
        });
    });
}
