/**
 * Configuración de Laravel Echo para Reverb
 * Reemplaza la configuración de beyondcode/laravel-websockets
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configuración de Reverb
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.MIX_REVERB_APP_KEY || 'local',
    wsHost: process.env.MIX_REVERB_HOST || '127.0.0.1',
    wsPort: process.env.MIX_REVERB_PORT || 8080,
    wssPort: process.env.MIX_REVERB_PORT || 8080,
    forceTLS: (process.env.MIX_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    enableLogging: process.env.NODE_ENV === 'development',
});

// Configuración alternativa para desarrollo local
if (process.env.NODE_ENV === 'local' || process.env.NODE_ENV === 'development') {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: 'local',
        wsHost: '127.0.0.1',
        wsPort: 8080,
        wssPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        enableLogging: true,
    });
}

export default window.Echo;
