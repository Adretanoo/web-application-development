import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    cluster: 'mt1',
    wsHost: '127.0.0.1',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws']
});

console.log('Echo OK');
