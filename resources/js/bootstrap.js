import _ from 'lodash';
window._ = _;

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

window.Pusher = Pusher;

// Enable Pusher logging
Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '8368e85f5faa47ff1b83',
    cluster: 'eu',
    forceTLS: true,
    encrypted: true,
    enabledTransports: ['ws', 'wss'],
    host: 'api-eu.pusher.com',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1],
        }
    }
});

// Debug Pusher connection
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Pusher connected');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Pusher error:', error);
});

window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('Connection states:', {
        previous: states.previous,
        current: states.current,
    });
});