/**
 * Push Notification Subscription Manager.
 *
 * Handles registering the Service Worker, requesting notification
 * permissions, and sending the PushSubscription to the server.
 */

const PushManager = (() => {
    'use strict';

    const VAPID_PUBLIC_KEY = document.querySelector('meta[name="vapid-public-key"]')?.content;
    const SW_PATH = '/sw.js';

    /**
     * Convert a URL-safe Base64 string to a Uint8Array (applicationServerKey).
     */
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
    }

    /**
     * Check if the browser supports push notifications.
     */
    function isSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    /**
     * Register the Service Worker.
     */
    async function registerServiceWorker() {
        return navigator.serviceWorker.register(SW_PATH);
    }

    /**
     * Subscribe the user to push notifications.
     */
    async function subscribe() {
        if (!isSupported()) {
            console.warn('[PushManager] Push notifications are not supported in this browser.');
            return;
        }

        if (!VAPID_PUBLIC_KEY) {
            console.error('[PushManager] VAPID public key not found in meta tag.');
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            console.info('[PushManager] Notification permission denied.');
            return;
        }

        try {
            const registration = await registerServiceWorker();

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
            });

            await sendSubscriptionToServer(subscription);
            console.info('[PushManager] Successfully subscribed to push notifications.');
        } catch (error) {
            console.error('[PushManager] Subscription failed:', error);
        }
    }

    /**
     * Unsubscribe from push notifications.
     */
    async function unsubscribe() {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            return;
        }

        await subscription.unsubscribe();
        await removeSubscriptionFromServer(subscription);
        console.info('[PushManager] Successfully unsubscribed from push notifications.');
    }

    /**
     * Send PushSubscription to the server for storage.
     */
    async function sendSubscriptionToServer(subscription) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(subscription.toJSON()),
        });

        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}`);
        }
    }

    /**
     * Remove PushSubscription from the server.
     */
    async function removeSubscriptionFromServer(subscription) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        await fetch('/push/unsubscribe', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ endpoint: subscription.endpoint }),
        });
    }

    /**
     * Check current subscription status.
     */
    async function getSubscriptionStatus() {
        if (!isSupported()) {
            return { supported: false, subscribed: false, permission: 'unsupported' };
        }

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        return {
            supported: true,
            subscribed: !!subscription,
            permission: Notification.permission,
        };
    }

    return {
        isSupported,
        subscribe,
        unsubscribe,
        getSubscriptionStatus,
    };
})();

export default PushManager;
