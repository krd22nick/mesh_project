import axios from 'axios';
import Echo from 'laravel-echo';
window.axios = axios;
window.Pusher = require('pusher-js');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'base64:rOZiRtzex0QoRqBqO6MRV1YFvrTJi6TDtaC9efxIsuY=',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});
