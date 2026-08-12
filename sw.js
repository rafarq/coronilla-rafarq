const CACHE_NAME = 'coronilla-v3';
const ASSETS = [
    './',
    './index.php',
    './script.js',
    './cards.json',
    './manifest.json',
    './icons/icon-180x180.png'
];

// Estrategia: red primero para navegación (HTML) y caché para el resto.
// Así los cambios del index.php se reflejan al recargar estando conectado,
// sin romper el uso offline.
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    // No cachear nunca la API del contador.
    if (request.url.includes('counter.php')) {
        return;
    }

    const isNavigation = request.mode === 'navigate';

    if (isNavigation) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match('./index.php')))
        );
    } else {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request))
        );
    }
});
