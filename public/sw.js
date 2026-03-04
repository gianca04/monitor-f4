'use strict';

/**
 * Service Worker para Web Push Notifications.
 *
 * Escucha eventos push y muestra notificaciones nativas del navegador.
 * También maneja clics en las notificaciones para redirigir al usuario.
 */

self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data ? event.data.json() : {};

    const title = data.title || 'Monitor F4';
    const options = {
        body: data.body || '',
        icon: data.icon || '/images/favicon.svg',
        badge: data.badge || '/images/favicon.svg',
        tag: data.tag || 'default',
        data: {
            url: data.url || '/',
        },
        requireInteraction: data.requireInteraction || false,
        actions: data.actions || [],
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // Focus an existing window if one is open on the same origin
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise open a new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

self.addEventListener('notificationclose', function (_event) {
    // Analytics or cleanup could go here
});
