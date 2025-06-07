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

// Enable Pusher logging - don't duplicate this setting
Pusher.logToConsole = true;

// Add global Pusher debug functions with enhanced capabilities
window.debugPusher = {
    // Get all subscribed channels
    getChannels: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            return window.Echo.connector.pusher.channels.channels;
        }
        return 'Echo or Pusher not initialized';
    },
    
    // Force reconnection with improved error handling
    reconnect: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            try {
                console.log('Manually reconnecting Pusher...');
                window.Echo.connector.pusher.connection.disconnect();
                setTimeout(() => {
                    window.Echo.connector.pusher.connection.connect();
                    console.log('Reconnection attempt initiated');
                }, 1000);
                return 'Reconnecting...';
            } catch (error) {
                console.error('Reconnection failed:', error);
                return 'Reconnection failed: ' + error.message;
            }
        }
        return 'Echo or Pusher not initialized';
    },
    
    // Get connection state
    getState: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            return window.Echo.connector.pusher.connection.state;
        }
        return 'Echo or Pusher not initialized';
    },
    
    // Check socket ID
    getSocketId: function() {
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            return window.Echo.connector.pusher.connection.socket_id;
        }
        return 'Echo or Pusher not initialized';
    }
};

// Get CSRF token from meta tag or cookie with improved error handling
const getCSRFToken = () => {
    try {
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (metaToken) {
            console.log('Using CSRF token from meta tag');
            return metaToken;
        }
        
        // Try to get from cookie
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        const cookieToken = match ? decodeURIComponent(match[1]) : null;
        if (cookieToken) {
            console.log('Using CSRF token from cookie');
            return cookieToken;
        }
        
        console.warn('No CSRF token found! This will cause authentication issues.');
        return '';
    } catch (error) {
        console.error('Error getting CSRF token:', error);
        return '';
    }
};

// Create Echo instance with improved configuration
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '8368e85f5faa47ff1b83',
    cluster: 'eu',
    forceTLS: true,
    encrypted: true,
    // Don't duplicate enabledTransports
    enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
    disableStats: false, // Enable stats for better debugging
    authEndpoint: '/broadcasting/auth',
    csrfToken: getCSRFToken(),
    auth: {
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    },
    // Enhanced connection handling
    activityTimeout: 120000, // 2 minutes - longer timeout to prevent disconnects
    pongTimeout: 30000,     // 30 seconds - longer pong timeout
    // Remove custom WebSocket host and port configuration to use Pusher's defaults
    disableStats: true,     // Disable stats which can cause issues
    enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'], // Use all available transports for better fallback
    // Reconnection strategy
    autoReconnect: true,
    reconnectionDelay: 1000,
    reconnectionDelayMax: 5000,
    reconnectionAttempts: 10
});

// Enhanced connection event handling
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Pusher connected successfully with socket ID:', 
                window.Echo.connector.pusher.connection.socket_id);
    // Store connection timestamp
    window.lastPusherConnection = new Date().getTime();
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.warn('❌ Pusher disconnected');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('❌ Pusher error:', error);
    // Attempt to reconnect after error
    setTimeout(() => window.debugPusher.reconnect(), 3000);
});

window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('Pusher connection state changed:', {
        previous: states.previous,
        current: states.current,
    });
    
    // Auto-reconnect if in failed or disconnected state for too long
    if (['failed', 'disconnected'].includes(states.current)) {
        setTimeout(() => {
            if (window.Echo.connector.pusher.connection.state === states.current) {
                console.log('Still in ' + states.current + ' state, attempting reconnection...');
                window.debugPusher.reconnect();
            }
        }, 5000);
    }
});

// Set up heartbeat with less aggressive reconnection to prevent navigation issues
const heartbeatInterval = setInterval(() => {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        const state = window.Echo.connector.pusher.connection.state;
        console.log('Pusher heartbeat check - Current state:', state);
        
        // Only reconnect if definitely disconnected, not during navigation
        if (state === 'disconnected' || state === 'failed') {
            console.warn('Connection disconnected during heartbeat, reconnecting...');
            window.debugPusher.reconnect();
        }
        
        // Less aggressive stale connection check
        const now = new Date().getTime();
        const lastConnection = window.lastPusherConnection || 0;
        if ((now - lastConnection) > 600000) { // 10 minutes instead of 5
            console.log('Connection may be stale, refreshing...');
            window.debugPusher.reconnect();
            window.lastPusherConnection = now;
        }
    }
}, 60000); // Check every minute

// Handle Inertia.js page transitions
document.addEventListener('inertia:before', () => {
    console.log('Inertia navigation started - pausing Echo connections');
    // Don't disconnect, just pause heartbeat during navigation
    clearInterval(heartbeatInterval);
});

document.addEventListener('inertia:finish', () => {
    console.log('Inertia navigation finished - resuming Echo connections');
    // Resume normal operation after navigation completes
    window.lastPusherConnection = new Date().getTime();
});

// Clean up Echo connections when page is unloaded
window.addEventListener('beforeunload', () => {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        console.log('Page unloading - cleaning up Echo connections');
        window.Echo.connector.pusher.disconnect();
    }
});