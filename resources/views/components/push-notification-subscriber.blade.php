{{--
    Push Notification Subscription Component.

    Este componente se inyecta en el layout de Filament a través de un
    render hook. Registra el Service Worker y solicita permisos de
    notificación al navegador de forma automática.
--}}

<div
    x-data="pushNotifications()"
    x-init="init()"
    style="display: none;"
></div>

<script>
    function pushNotifications() {
        return {
            vapidPublicKey: @js(config('webpush.vapid.public_key')),

            async init() {
                if (!this.isSupported()) return;

                await this.registerServiceWorker();

                if (Notification.permission === 'default') {
                    // Small delay to avoid blocking page load
                    setTimeout(() => this.subscribe(), 3000);
                } else if (Notification.permission === 'granted') {
                    await this.subscribe();
                }
            },

            isSupported() {
                return 'serviceWorker' in navigator
                    && 'PushManager' in window
                    && 'Notification' in window;
            },

            urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                const rawData = atob(base64);
                return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
            },

            async registerServiceWorker() {
                try {
                    await navigator.serviceWorker.register('/sw.js');
                } catch (error) {
                    console.error('[Push] Service Worker registration failed:', error);
                }
            },

            async subscribe() {
                try {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') return;

                    const registration = await navigator.serviceWorker.ready;
                    let subscription = await registration.pushManager.getSubscription();

                    if (!subscription) {
                        subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey),
                        });
                    }

                    await this.sendToServer(subscription);
                } catch (error) {
                    console.error('[Push] Subscription failed:', error);
                }
            },

            async sendToServer(subscription) {
                const data = subscription.toJSON();

                await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        endpoint: data.endpoint,
                        keys: {
                            p256dh: data.keys.p256dh,
                            auth: data.keys.auth,
                        },
                    }),
                });
            },
        };
    }
</script>
