<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Back button -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center">
                <Link :href="route('profile.edit')" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <span>Back to Profile</span>
                </Link>
            </div>
        </div>

        <!-- Chat container -->
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <!-- Header -->
                <div class="border-b px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img
                                :src="otherUser.profile_photo_url"
                                :alt="otherUser.name"
                                class="w-10 h-10 rounded-full mr-4"
                            />
                            <div>
                                <h2 class="text-lg font-semibold">{{ otherUser.name }}</h2>
                                <p v-if="skillExchange" class="text-sm text-gray-600">
                                    {{ skillExchange }}
                                </p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ messagesList.length }} messages
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div 
                    ref="messagesContainer"
                    class="px-6 py-4 h-[500px] overflow-y-auto"
                >
                    <!-- Use renderKey to force re-rendering when messages change -->
                    <div :key="renderKey">
                        <template v-for="(message, index) in groupedMessages" :key="message.id || 'msg-' + index">
                            <!-- Date separator -->
                            <div 
                                v-if="index === 0 || message.date !== groupedMessages[index - 1].date"
                                class="text-center my-4"
                            >
                                <span class="px-4 py-1 bg-gray-100 rounded-full text-sm text-gray-600">
                                    {{ message.date }}
                                </span>
                            </div>

                            <!-- Message -->
                            <div 
                                :class="[
                                    'flex mb-4',
                                    message.is_mine ? 'justify-end' : 'justify-start'
                                ]"
                            >
                                <div 
                                    :class="[
                                        'max-w-[70%] rounded-lg px-4 py-2',
                                        message.is_mine 
                                            ? 'bg-gray-800 text-white rounded-br-none'
                                            : 'bg-gray-100 text-gray-900 rounded-bl-none'
                                    ]"
                                >
                                    <p>{{ message.content }}</p>
                                    <p 
                                        :class="[
                                            'text-xs mt-1',
                                            message.is_mine ? 'text-gray-300' : 'text-gray-500'
                                        ]"
                                    >
                                        {{ message.time }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="border-t px-6 py-4">
                    <form @submit.prevent="sendMessage" class="flex gap-4">
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Type your message..."
                            class="flex-1 rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                        />
                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
                            :disabled="!newMessage.trim()"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Echo from 'laravel-echo';
import axios from 'axios';

const props = defineProps({
    auth: Object,
    otherUser: Object,
    messages: Array,
    skillExchange: String,
});

const messagesContainer = ref(null);
const newMessage = ref('');

// Use a simple ref for messages with a unique key for each render
const messagesList = ref([...props.messages]);
const renderKey = ref(0); // Force re-render when this changes

// Debug the initial messages
console.log('Initial messages:', messagesList.value.length);

// Group messages by date for display
const groupedMessages = computed(() => {
    console.log('Computing grouped messages with', messagesList.value.length, 'messages', 'render key:', renderKey.value);
    
    // Map each message to include a formatted date
    return messagesList.value.map(message => {
        // Ensure we have a valid date string
        let dateStr = message.date;
        if (!dateStr) {
            dateStr = new Date().toISOString().split('T')[0];
        }
        
        // Create a new object to avoid mutation issues
        return {
            ...message,
            date: new Date(dateStr).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            }),
        };
    });
});

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const sendMessage = () => {
    if (newMessage.value.trim() === '') return;
    
    const messageContent = newMessage.value.trim();
    
    // Clear the form immediately for better UX
    newMessage.value = '';

    // Create a new message object
    const newMsg = {
        id: 'temp-' + Date.now(), // Temporary ID with prefix to avoid conflicts
        content: messageContent,
        time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
        date: new Date().toISOString().split('T')[0],
        is_mine: true,
    };
    
    // Add the new message to our messages list
    messagesList.value.push(newMsg);
    
    // Increment the render key to force a complete re-render
    renderKey.value++;
    
    console.log('Added outgoing message, new count:', messagesList.value.length, 'render key:', renderKey.value);
    
    // Scroll to bottom immediately after adding the message
    nextTick(() => {
        scrollToBottom();
    });

    // Send the message to the server
    router.post(route('messages.store', props.otherUser.id), {
        content: messageContent,
        skill_exchange: props.skillExchange,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Message sent successfully');
        },
        onError: (errors) => {
            console.error('Error sending message:', errors);
        }
    });
};

let echo = null;

// Function to mark all messages in this conversation as read
const markMessagesAsRead = () => {
    axios.post(route('messages.mark-conversation-read', props.otherUser.id))
        .then(() => {
            console.log('Marked all messages as read');
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
        });
};

// Track our channel subscriptions
let userChannel = null;
let chatChannel = null;

onMounted(() => {
    scrollToBottom();
    
    // Mark messages as read when conversation is opened
    markMessagesAsRead();
    
    const userId = props.auth.user.id;
    const otherUserId = props.otherUser.id;
    
    console.log('Setting up Echo listeners for user:', userId, 'and other user:', otherUserId);
    
    // First, unsubscribe from any existing channels to avoid duplicates
    if (userChannel) {
        console.log('Unsubscribing from existing user channel');
        userChannel.unsubscribe();
        userChannel = null;
    }
    
    if (chatChannel) {
        console.log('Unsubscribing from existing chat channel');
        chatChannel.unsubscribe();
        chatChannel = null;
    }
    
    // Track processed message IDs to prevent duplicates
    const processedMessageIds = new Set();
    
    // Helper function to handle incoming messages
    const handleIncomingMessage = (data) => {
        // Safety check for data structure
        if (!data || !data.message) {
            return false;
        }
        
        // Check if we've already processed this message ID
        if (processedMessageIds.has(data.message.id)) {
            return false;
        }
        
        // Check if message is from the other user we're chatting with
        if (parseInt(data.message.user_id) === parseInt(otherUserId)) {
            // Check if message already exists in the list
            const messageExists = messagesList.value.some(msg => msg.id === data.message.id);
            if (messageExists) {
                return false;
            }
            
            // Add this message ID to our processed set
            processedMessageIds.add(data.message.id);
            
            // Create a new message object
            const newMessage = {
                id: data.message.id,
                content: data.message.content,
                time: data.message.time,
                date: data.message.date,
                is_mine: false,
            };
            
            // Add the message to our messages list
            messagesList.value.push(newMessage);
            
            // Increment the render key to force a re-render
            renderKey.value++;
            
            // Mark the message as read
            markMessagesAsRead();
            
            // Scroll to bottom
            nextTick(() => {
                scrollToBottom();
            });
            
            return true;
        }
        
        return false;
    };
    
    // Listen on the user's personal channel for messages
    userChannel = window.Echo.private(`user.${userId}`);
    
    // Listen for all events on this channel to debug
    userChannel.listenToAll((eventName, data) => {
        console.log(`🔔 ANY EVENT on user.${userId}:`, { event_name: eventName, data });
    });
    
    // IMPORTANT: Also subscribe directly to the recipient's user channel
    // This ensures we receive messages sent to the other user
    const recipientUserChannel = window.Echo.private(`user.${otherUserId}`);
    
    // Listen for all events on the recipient's user channel
    recipientUserChannel.listenToAll((eventName, data) => {
        console.log(`🔔 ANY EVENT on recipient's user.${otherUserId}:`, { event_name: eventName, data });
    });
    
    // Add specific listeners to the recipient's user channel
    recipientUserChannel.listen('.MessageSent', (data) => {
        console.log(`📬 RECEIVED '.MessageSent' on recipient's user.${otherUserId}:`, data);
        handleIncomingMessage(data);
    });
    
    // Make sure we're properly subscribed before adding listeners
    userChannel.subscribed(() => {
        console.log(`✅ Successfully subscribed to private-user.${userId}`);
        
        // Try all possible event name formats
        userChannel.listen('MessageSent', (data) => {
            console.log(`📬 RECEIVED 'MessageSent' on user.${userId}:`, data);
            handleIncomingMessage(data);
        });
        
        userChannel.listen('.MessageSent', (data) => {
            console.log(`📬 RECEIVED '.MessageSent' on user.${userId}:`, data);
            handleIncomingMessage(data);
        });
        
        userChannel.listen('App\\Events\\MessageSent', (data) => {
            console.log(`📬 RECEIVED 'App\\Events\\MessageSent' on user.${userId}:`, data);
            handleIncomingMessage(data);
        });
    });
    
    console.log(`Setting up listener on private-chat.${userId}`);
    
    // Listen on the chat channel as well
    chatChannel = window.Echo.private(`chat.${userId}`);
    
    // Listen for all events on this channel to debug
    chatChannel.listenToAll((eventName, data) => {
        console.log(`🔔 ANY EVENT on chat.${userId}:`, { event_name: eventName, data });
    });
    
    // IMPORTANT: Also subscribe directly to the recipient's channel
    // This ensures we receive messages sent to the other user
    const recipientChannel = window.Echo.private(`chat.${otherUserId}`);
    
    // Listen for all events on the recipient's channel
    recipientChannel.listenToAll((eventName, data) => {
        console.log(`🔔 ANY EVENT on recipient's chat.${otherUserId}:`, { event_name: eventName, data });
    });
    
    // Add specific listeners to the recipient's channel
    recipientChannel.listen('.MessageSent', (data) => {
        console.log(`📬 RECEIVED '.MessageSent' on recipient's chat.${otherUserId}:`, data);
        handleIncomingMessage(data);
    });
    
    // Make sure we're properly subscribed before adding listeners
    chatChannel.subscribed(() => {
        console.log(`✅ Successfully subscribed to private-chat.${userId}`);
        
        // Try all possible event name formats
        chatChannel.listen('MessageSent', (data) => {
            console.log(`📬 RECEIVED 'MessageSent' on chat.${userId}:`, data);
            handleIncomingMessage(data);
        });
        
        chatChannel.listen('.MessageSent', (data) => {
            console.log(`📬 RECEIVED '.MessageSent' on chat.${userId}:`, data);
            handleIncomingMessage(data);
        });
        
        chatChannel.listen('App\\Events\\MessageSent', (data) => {
            console.log(`📬 RECEIVED 'App\\Events\\MessageSent' on chat.${userId}:`, data);
            handleIncomingMessage(data);
        });
    });
    
    // Add more detailed Pusher debugging
    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('🔄 Pusher connection state changed:', states);
    });
    
    // Debug channel subscriptions
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Pusher connected successfully!');
        console.log('Subscribed channels:', window.Echo.connector.pusher.channels.channels);
    });
    
    // Debug subscription errors
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
    });
});

onUnmounted(() => {
    // Clean up Echo listeners
    const userId = props.auth.user.id;
    const otherUserId = props.user.id;
    
    console.log('Component unmounting, cleaning up all channel subscriptions');
    
    // Unsubscribe from user channel using our channel variables
    if (userChannel) {
        userChannel.stopListening('.MessageSent');
        userChannel.stopListening('MessageSent');
        userChannel.stopListening('App\\Events\\MessageSent');
        userChannel.unsubscribe();
        console.log(`Stopped listening on user.${userId}`);
    }
    
    // Unsubscribe from chat channel using our channel variables
    if (chatChannel) {
        chatChannel.stopListening('.MessageSent');
        chatChannel.stopListening('MessageSent');
        chatChannel.stopListening('App\\Events\\MessageSent');
        chatChannel.unsubscribe();
        console.log(`Stopped listening on chat.${userId}`);
    }
    
    // Also manually unsubscribe from Pusher channels to be thorough
    const pusher = window.Echo.connector.pusher;
    
    // Unsubscribe from our own channels
    pusher.unsubscribe(`private-user.${userId}`);
    pusher.unsubscribe(`private-chat.${userId}`);
    
    // Unsubscribe from recipient channels
    pusher.unsubscribe(`private-user.${otherUserId}`);
    pusher.unsubscribe(`private-chat.${otherUserId}`);
    
    console.log('Cleaned up all Echo listeners');
});
</script>
