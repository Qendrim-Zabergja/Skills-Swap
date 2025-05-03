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

onMounted(() => {
    scrollToBottom();
    
    // Initialize Echo and listen for messages
    echo = window.Echo.private(`chat.${props.auth.user.id}`)
        .listen('MessageSent', (event) => {
            console.log('Received message event:', event);
            
            // Check if message is from the other user we're chatting with
            if (parseInt(event.message.user_id) === parseInt(props.otherUser.id)) {
                console.log('Adding message from user:', event.message.user_id);
                console.log('Message content:', event.message.content);
                
                // Add new message at the end
                localMessages.value.push({
                    id: event.message.id,
                    content: event.message.content,
                    time: event.message.time,
                    date: event.message.date,
                    is_mine: false,
                });
                
                nextTick(() => {
                    scrollToBottom();
                });
            } else {
                console.log('Message not from current chat partner', {
                    messageFromId: event.message.user_id,
                    currentChatPartnerId: props.otherUser.id
                });
            }
        });

    // Debug channel subscription
    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('Pusher state changed:', states);
    });

    echo.on('subscription_succeeded', () => {
        console.log('Successfully subscribed to chat channel:', `chat.${props.auth.user.id}`);
    });

    echo.on('subscription_error', (error) => {
        console.error('Failed to subscribe to chat channel:', error);
    });
});

onUnmounted(() => {
    // Clean up Echo listener
    if (echo) {
        echo.stopListening('MessageSent');
        window.Echo.leave(`chat.${props.auth.user.id}`);
    }
});
</script>
