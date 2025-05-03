<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
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
    const message = newMessage.value.trim();
    if (!message) return;

    router.post(route('messages.store', props.otherUser.id), {
        content: message,
        skill_exchange: props.skillExchange,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Add message to local state immediately
            localMessages.value.unshift({
                id: Date.now(), // temporary ID
                content: message,
                time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
                date: new Date().toISOString(),
                is_mine: true,
            });
            newMessage.value = '';
            nextTick(scrollToBottom);
        },
    });
};

let echo = null;

onMounted(() => {
    scrollToBottom();
    
    // Initialize Echo and listen for messages
    echo = window.Echo.private(`chat.${props.auth.user.id}`)
        .listen('MessageSent', (e) => {
            if (e.message.user_id === props.otherUser.id) {
                localMessages.value.unshift({
                    id: e.message.id,
                    content: e.message.content,
                    time: e.message.time,
                    date: e.message.date,
                    is_mine: false,
                });
                scrollToBottom();
            }
        });
});

onUnmounted(() => {
    // Clean up Echo listener
    if (echo) {
        echo.stopListening('MessageSent');
    }
});
</script>
