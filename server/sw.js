// Asmira Ops - Service Worker
const CACHE_NAME = 'asmira-ops-v1';
const OFFLINE_URL = '/offline.html';

// Sadece shell dosyalarını önbelleğe al
const PRECACHE_URLS = [
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/img/asmira-marine-logo.png',
    '/assets/img/asmira-energy-logo.png',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    // API isteklerini cache'leme — her zaman network'e git
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Statik dosyaları cache'e yaz
                if (response.ok && (event.request.url.match(/\.(css|js|png|jpg|ico|woff2?)$/))) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});
