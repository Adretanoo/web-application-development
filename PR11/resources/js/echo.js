import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '{{ config("reverb.apps.apps.0.key") }}',
    wsHost: '127.0.0.1',
    wsPort: 8080, // постав свій порт Reverb
    wssPort: 8080,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws'],
    cluster: 'mt1'
});


console.log('Echo (Reverb) initialized successfully');
