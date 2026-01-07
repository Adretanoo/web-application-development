import Echo from 'laravel-echo';
import Reverb from './reverb';

window.Echo = new Echo({
    broadcaster: Reverb,
});
