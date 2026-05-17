const CACHE_NAME = 'holding-erp-shell-v2';
const STATIC_ASSETS = [
    '/',
    '/holding',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)),
        )),
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const acceptsHtml = event.request.headers.get('accept')?.includes('text/html');

    if (event.request.mode === 'navigate' || acceptsHtml) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));

                    return response;
                })
                .catch(() => caches.match(event.request)),
        );

        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached ?? fetch(event.request)),
    );
});
