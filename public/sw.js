/*
 * Service Worker des Qualitools (registriert in resources/js/pwa.js).
 *
 * Bewusst klein: Seiten werden NIE gespeichert, weil sie Qualis und Rückmeldungen (Personendaten) zeigen und Handys
 * im Lager auch geteilt werden. Gespeichert werden nur die Dateien aus /build/ (Name enthält einen Hash, ändern sich
 * also nie), Icons und die Offline-Seite. Ohne Netz zeigt eine Seitenanfrage offline.html.
 */
const CACHE = 'qualitool-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/img/logo.svg', '/android-chrome-192x192.png'];
const MAX_ENTRIES = 80;

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

/** Älteste Einträge entfernen, damit alte Builds den Speicher nicht füllen. */
async function trim(cache) {
    const keys = await cache.keys();
    await Promise.all(keys.slice(0, Math.max(0, keys.length - MAX_ENTRIES)).map((key) => cache.delete(key)));
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }
    const response = await fetch(request);
    if (response.ok) {
        const cache = await caches.open(CACHE);
        await cache.put(request, response.clone());
        await trim(cache);
    }
    return response;
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') {
        return;
    }
    const url = new URL(request.url);
    if (url.origin !== self.location.origin) {
        return;
    }
    if (request.mode === 'navigate') {
        // Seiten immer vom Server; nur ohne Netz die Offline-Seite
        event.respondWith(fetch(request).catch(() => caches.match(OFFLINE_URL)));
        return;
    }
    if (url.pathname.startsWith('/build/') || /\.(png|svg|ico|webp|woff2?)$/.test(url.pathname)) {
        event.respondWith(cacheFirst(request));
    }
});
