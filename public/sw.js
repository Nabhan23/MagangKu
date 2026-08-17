/* Service Worker MagangKu - PWA */
const CACHE = 'magangku-v1';

self.addEventListener('install', (e) => {
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (e) => {
    const { request } = e;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== location.origin) return;

    // Navigasi halaman: network-first (selalu segar saat online), fallback cache saat offline
    if (request.mode === 'navigate') {
        e.respondWith(
            fetch(request)
                .then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                    return res;
                })
                .catch(() =>
                    caches.match(request).then((cached) => cached || caches.match('/'))
                )
        );
        return;
    }

    // Aset statis (build assets ber-hash, gambar): cache-first
    const cacheable =
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/img/') ||
        url.pathname.startsWith('/storage/') ||
        url.pathname === '/manifest.json';

    e.respondWith(
        caches.match(request).then((cached) => {
            if (cached) return cached;
            return fetch(request).then((res) => {
                if (res.ok && cacheable) {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                }
                return res;
            });
        })
    );
});
