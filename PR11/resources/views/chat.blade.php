<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Чат</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-10 px-4">
        <div id="messages" class="bg-white border border-gray-300 rounded-lg h-96 overflow-y-auto p-4 mb-6 space-y-3">
            @foreach($messages as $m)
                <div class="bg-gray-50 rounded-lg p-3">
                    <strong class="text-blue-600">{{ $m->user->name }}:</strong>
                    <span>{{ $m->message }}</span>
                </div>
            @endforeach
        </div>

        <form id="chat-form" class="flex gap-3">
            @csrf
            <input id="message-input" type="text" autocomplete="off" required
                   placeholder="Напишіть повідомлення..."
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Надіслати
            </button>
        </form>
    </div>

    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.17/dist/echo.iife.min.js"></script>

    <script>
        const messagesDiv = document.getElementById('messages');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('message-input');

        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST') ?? 'localhost' }}',
            wsPort: '{{ env('REVERB_PORT') ?? 8080 }}',
            wssPort: '{{ env('REVERB_PORT') ?? 8080 }}',
            forceTLS: '{{ env('REVERB_SCHEME') }}' === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });

        Echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket підключено успішно!');
        });

        Echo.connector.pusher.connection.bind('failed', () => {
            console.error('WebSocket підключення провалено!');
        });

        Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('WebSocket помилка:', err);
        });

        Echo.channel('chat')
            .listen('.MessageSent', (e) => {
                console.log('Отримано повідомлення:', e);

                const div = document.createElement('div');
                div.className = 'bg-gray-50 rounded-lg p-3';
                div.innerHTML = `<strong class="text-blue-600">${e.user}:</strong> <span>${e.message}</span>`;
                messagesDiv.appendChild(div);
                div.scrollIntoView({ behavior: 'smooth' });
            });
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            const myDiv = document.createElement('div');
            messagesDiv.appendChild(myDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            input.value = '';

            try {
                const res = await fetch("{{ route('chat.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                });

                if (!res.ok) throw new Error('Серверна помилка');
            } catch (err) {
                console.error(err);
                alert('Помилка відправки');
                messagesDiv.removeChild(myDiv);
            }
        });
    </script>

</x-app-layout>
