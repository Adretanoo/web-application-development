import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Echo = new Echo({
    broadcaster: 'pusher', // Reverb сумісний із Pusher API
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    client: Pusher, // важливо!
});

console.log('Echo initialized successfully with Reverb');

Alpine.start();
