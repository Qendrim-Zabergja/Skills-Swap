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

// Enable Pusher logging - set to maximum verbosity
Pusher.logToConsole = true;

// Add global Pusher debug functions
window.debugPusher = {
    // Get all subscribed channels
    getChannels: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            return window.Echo.connector.pusher.channels.channels;
        }
        return 'Echo or Pusher not initialized';
    },
    
    // Force reconnection
    reconnect: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.disconnect();
            setTimeout(() => window.Echo.connector.pusher.connection.connect(), 1000);
            return 'Reconnecting...';
        }
        return 'Echo or Pusher not initialized';
    }
};

// Enable Pusher logging
Pusher.logToConsole = true;

// Get CSRF token from meta tag or cookie
const getCSRFToken = () => {
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken) {
        console.log('Using CSRF token from meta tag:', metaToken);
        return metaToken;
    }
    
    // Try to get from cookie
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    const cookieToken = match ? decodeURIComponent(match[1]) : null;
    if (cookieToken) {
        console.log('Using CSRF token from cookie:', cookieToken);
        return cookieToken;
    }
    
    console.warn('No CSRF token found!');
    return '';
};

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '8368e85f5faa47ff1b83',
    cluster: 'eu',
    forceTLS: true,
    encrypted: true,
    enabledTransports: ['ws', 'wss'],
    disableStats: true, // Disable stats which can cause issues
    authEndpoint: '/broadcasting/auth', // Explicitly set the auth endpoint
    csrfToken: getCSRFToken(), // Add CSRF token directly
    auth: {
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    },
    // Enhanced options for better connection handling
    activityTimeout: 30000, // Increase timeout to prevent disconnects
    pongTimeout: 15000,
    // Enable detailed logging
    logToConsole: true,
    // Automatically reconnect if connection is lost
    enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
    // Retry connection if it fails
    autoReconnect: true
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