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
                </div>

                <!-- Messages -->
                <div 
                    ref="messagesContainer"
                    class="px-6 py-4 h-[500px] overflow-y-auto"
                >
                    <template v-for="(message, index) in groupedMessages" :key="message.date">
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
    otherUser: Object,
    messages: Array,
    skillExchange: {
        type: String,
        default: null,
    },
    auth: Object,
});

const newMessage = ref('');
const messagesContainer = ref(null);
const localMessages = ref([...props.messages]);

const groupedMessages = computed(() => {
    return localMessages.value.map(message => ({
        ...message,
        date: new Date(message.date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }),
    }));
});

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const sendMessage = () => {
    if (newMessage.value.trim() === '') return;

    router.post(route('messages.store', props.otherUser.id), {
        content: newMessage.value,
        skill_exchange: props.skillExchange,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Add the message to local state immediately
            localMessages.value.push({
                id: Date.now(), // Temporary ID
                content: newMessage.value,
                time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                date: new Date().toISOString().split('T')[0],
                is_mine: true,
            });
            
            // Clear the form
            newMessage.value = '';
            
            // Scroll to bottom after the message is added
            nextTick(() => {
                scrollToBottom();
            });
        },
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

onMounted(() => {
    scrollToBottom();
    
    // Mark messages as read when conversation is opened
    markMessagesAsRead();
    
    // Set up direct Pusher listeners instead of using Echo
    // This ensures we're listening on the exact channel names from the logs
    const userId = props.auth.user.id;
    const otherUserId = props.otherUser.id;
    
    // Listen for incoming messages on the user's channel
    const myChannelName = `private-chat.${userId}`;
    console.log(`Directly subscribing to channel: ${myChannelName}`);
    
    // Get the raw Pusher instance
    const pusher = window.Echo.connector.pusher;
    
    // Subscribe to my channel
    const myChannel = pusher.subscribe(myChannelName);
    myChannel.bind('MessageSent', (data) => {
        console.log(`Received message on ${myChannelName}:`, data);
        
        // Check if message is from the other user we're chatting with
        if (parseInt(data.message.user_id) === parseInt(otherUserId)) {
            console.log('Adding message from user:', data.message.user_id);
            
            // Add new message to the conversation
            localMessages.value.push({
                id: data.message.id,
                content: data.message.content,
                time: data.message.time,
                date: data.message.date,
                is_mine: false,
            });
            
            nextTick(() => {
                scrollToBottom();
            });
        }
    });
    
    // Subscribe to other user's channel
    const otherChannelName = `private-chat.${otherUserId}`;
    console.log(`Directly subscribing to channel: ${otherChannelName}`);
    
    const otherChannel = pusher.subscribe(otherChannelName);
    otherChannel.bind('MessageSent', (data) => {
        console.log(`Received message on ${otherChannelName}:`, data);
        
        // Check if this message is relevant to our conversation
        if (parseInt(data.message.recipient_id) === parseInt(userId) && 
            parseInt(data.message.user_id) === parseInt(otherUserId)) {
            
            // Add new message to the conversation
            localMessages.value.push({
                id: data.message.id,
                content: data.message.content,
                time: data.message.time,
                date: data.message.date,
                is_mine: false,
            });
            
            nextTick(() => {
                scrollToBottom();
            });
        }
    });
    
    // Debug Pusher connection
    pusher.connection.bind('state_change', (states) => {
        console.log('Pusher state changed:', states);
    });
});

onUnmounted(() => {
    // Clean up Pusher subscriptions
    const pusher = window.Echo.connector.pusher;
    
    // Unsubscribe from my channel
    const myChannelName = `private-chat.${props.auth.user.id}`;
    pusher.unsubscribe(myChannelName);
    console.log(`Unsubscribed from channel: ${myChannelName}`);
    
    // Unsubscribe from other user's channel
    const otherChannelName = `private-chat.${props.otherUser.id}`;
    pusher.unsubscribe(otherChannelName);
    console.log(`Unsubscribed from channel: ${otherChannelName}`);
});
</script>
