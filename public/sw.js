// Service Worker for Delivery PWA
const CACHE_NAME = 'delivery-v2';
const RUNTIME_CACHE = 'delivery-runtime-v2';

// Ресурсы для предварительного кэширования (только локальные)
const PRECACHE_URLS = [
    '/',
    '/catalog',
    '/cart',
    '/login',
    '/manifest.json'
];

// Внешние ресурсы для кэширования при первом запросе
const EXTERNAL_URLS = [
    'https://cdn.tailwindcss.com',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// Установка Service Worker
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Precaching local resources');
                // Кэшируем только локальные ресурсы
                return cache.addAll(PRECACHE_URLS);
            })
            .then(() => {
                console.log('[SW] Installation complete');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[SW] Precache failed:', error);
                // Даже при ошибке продолжаем установку
                return self.skipWaiting();
            })
    );
});

// Активация Service Worker
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => {
                            return cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE;
                        })
                        .map((cacheName) => {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[SW] Activation complete');
                return self.clients.claim();
            })
    );
});

// Стратегия кэширования
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Пропускаем POST запросы и API запросы
    if (request.method !== 'GET') {
        return;
    }
    
    // API запросы - Network First с fallback на кэш
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    // Статические ресурсы - Cache First
    if (url.origin !== location.origin || 
        url.pathname.endsWith('.css') || 
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.woff2')) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    // HTML страницы - Network First
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    // По умолчанию - Network First
    event.respondWith(networkFirst(request));
});

// Cache First стратегия
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        // Обновляем кэш в фоне
        fetch(request)
            .then((response) => {
                if (response.ok) {
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(request, response);
                    });
                }
            })
            .catch(() => {});
        
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        return new Response('Offline', { status: 503 });
    }
}

// Network First стратегия
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Пробуем получить из кэша
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Возвращаем офлайн страницу для HTML запросов
        if (request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/');
        }
        
        return new Response(JSON.stringify({ error: 'Offline' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

// Обработка push-уведомлений
self.addEventListener('push', (event) => {
    console.log('[SW] Push received');
    
    let data = { title: 'Delivery', body: 'Новое уведомление' };
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }
    
    const options = {
        body: data.body,
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/'
        },
        actions: data.actions || [
            { action: 'open', title: 'Открыть' },
            { action: 'close', title: 'Закрыть' }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Обработка клика по уведомлению
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'close') {
        return;
    }
    
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Ищем уже открытое окно
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Открываем новое окно
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Фоновая синхронизация
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-orders') {
        event.waitUntil(syncOrders());
    }
    
    if (event.tag === 'sync-cart') {
        event.waitUntil(syncCart());
    }
});

// Синхронизация заказов
async function syncOrders() {
    try {
        const pendingOrders = await getPendingOrders();
        
        for (const order of pendingOrders) {
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(order)
            });
            
            if (response.ok) {
                await removePendingOrder(order.id);
            }
        }
    } catch (error) {
        console.error('[SW] Sync orders failed:', error);
    }
}

// Синхронизация корзины
async function syncCart() {
    // Заглушка для будущей реализации
    console.log('[SW] Cart sync triggered');
}

// Вспомогательные функции для IndexedDB
function getPendingOrders() {
    return new Promise((resolve) => {
        resolve([]);
    });
}

function removePendingOrder(orderId) {
    return Promise.resolve();
}

// Обработка сообщений от страницы
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data === 'skipWaiting') {
        self.skipWaiting();
    }
    
    if (event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(RUNTIME_CACHE)
                .then((cache) => cache.addAll(event.data.urls))
        );
    }
});

console.log('[SW] Service Worker loaded');