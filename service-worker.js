const CACHE_NAME = 'evoucher-v1';
const RUNTIME_CACHE = 'evoucher-runtime-v1';
const OFFLINE_URL = '/offline.html';

// Files to cache on install
const PRECACHE_URLS = [
  '/',
  '/offline.html',
  '/css/app.css',
  '/js/app.js',
  '/images/icon-192.png',
  '/images/icon-512.png',
];

// Install event - cache essential files
self.addEventListener('install', (event) => {
  console.log('Service Worker installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('Caching essential files');
      return cache.addAll(PRECACHE_URLS).catch((err) => {
        console.warn('Some files could not be cached:', err);
        // Continue even if some files fail
        return Promise.resolve();
      });
    }).then(() => {
      console.log('Service Worker installed');
      return self.skipWaiting();
    })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker activating...');
  
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('Service Worker activated');
      return self.clients.claim();
    })
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Skip cross-origin requests
  if (url.origin !== location.origin) {
    return;
  }

  // Handle API requests differently
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Cache successful API responses
          if (response.ok) {
            const cache = caches.open(RUNTIME_CACHE);
            cache.then((c) => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          // Return cached response if network fails
          return caches.match(request);
        })
    );
    return;
  }

  // For HTML pages, try network first, then cache
  if (request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response.ok) {
            const cache = caches.open(RUNTIME_CACHE);
            cache.then((c) => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          return caches.match(request).then((cached) => {
            return cached || caches.match(OFFLINE_URL);
          });
        })
    );
    return;
  }

  // For other resources (CSS, JS, images), use cache-first strategy
  event.respondWith(
    caches.match(request).then((cached) => {
      return (
        cached ||
        fetch(request).then((response) => {
          if (response.ok) {
            const cache = caches.open(RUNTIME_CACHE);
            cache.then((c) => c.put(request, response.clone()));
          }
          return response;
        })
      );
    })
  );
});

// Handle messages from clients
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.delete(RUNTIME_CACHE).then(() => {
      console.log('Runtime cache cleared');
    });
  }
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-vouchers') {
    event.waitUntil(syncVouchers());
  }
  if (event.tag === 'sync-redemptions') {
    event.waitUntil(syncRedemptions());
  }
});

async function syncVouchers() {
  try {
    const cache = await caches.open(RUNTIME_CACHE);
    const response = await fetch('/api/vouchers');
    if (response.ok) {
      await cache.put('/api/vouchers', response.clone());
    }
  } catch (error) {
    console.error('Sync vouchers failed:', error);
  }
}

async function syncRedemptions() {
  try {
    const cache = await caches.open(RUNTIME_CACHE);
    const response = await fetch('/api/redemptions');
    if (response.ok) {
      await cache.put('/api/redemptions', response.clone());
    }
  } catch (error) {
    console.error('Sync redemptions failed:', error);
  }
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
  if (event.tag === 'update-data') {
    event.waitUntil(updateData());
  }
});

async function updateData() {
  try {
    const cache = await caches.open(RUNTIME_CACHE);
    
    // Update various data endpoints
    const endpoints = [
      '/api/vouchers',
      '/api/food',
      '/api/notifications',
    ];
    
    for (const endpoint of endpoints) {
      try {
        const response = await fetch(endpoint);
        if (response.ok) {
          await cache.put(endpoint, response.clone());
        }
      } catch (error) {
        console.error(`Failed to update ${endpoint}:`, error);
      }
    }
  } catch (error) {
    console.error('Update data failed:', error);
  }
}
