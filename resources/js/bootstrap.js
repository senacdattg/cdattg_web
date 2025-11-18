/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const obtenerValorConfig = (clavesEnv = [], metaName) => {
    const envSource = typeof import.meta !== 'undefined' && import.meta.env ? import.meta.env : {};
    for (const clave of clavesEnv) {
        const valor = envSource?.[clave];
        if (typeof valor === 'string' && valor.trim() !== '') {
            return valor;
        }
    }

    if (typeof process !== 'undefined' && process.env) {
        for (const clave of clavesEnv) {
            const valor = process.env[clave];
            if (typeof valor === 'string' && valor.trim() !== '') {
                return valor;
            }
        }
    }

    if (typeof document !== 'undefined' && metaName) {
        const metaTag = document.head?.querySelector(`meta[name="${metaName}"]`);
        if (metaTag?.content?.trim()) {
            return metaTag.content.trim();
        }
    }

    return null;
};

const pusherKey = obtenerValorConfig(['VITE_PUSHER_APP_KEY', 'MIX_PUSHER_APP_KEY'], 'pusher-key');
const pusherCluster = obtenerValorConfig(['VITE_PUSHER_APP_CLUSTER', 'MIX_PUSHER_APP_CLUSTER'], 'pusher-cluster') || 'mt1';
const pusherHost = obtenerValorConfig(['VITE_PUSHER_HOST', 'MIX_PUSHER_HOST'], 'pusher-host') || window.location.hostname;
const pusherPort = Number(obtenerValorConfig(['VITE_PUSHER_PORT', 'MIX_PUSHER_PORT'], 'pusher-port')) || 6001;
const pusherScheme = obtenerValorConfig(['VITE_PUSHER_SCHEME', 'MIX_PUSHER_SCHEME'], 'pusher-scheme') || 'http';

window.Pusher = Pusher;

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: pusherScheme === 'https',
        wsHost: pusherHost,
        wsPort: pusherPort,
        wssPort: pusherScheme === 'https' ? pusherPort : undefined,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
} else {
    // Solo mostrar warning en desarrollo
    if (process.env.NODE_ENV === 'development' || import.meta.env?.MODE === 'development') {
        console.warn('Pusher no está configurado: se omitió la inicialización de Laravel Echo.');
    }
    window.Echo = null;
}
