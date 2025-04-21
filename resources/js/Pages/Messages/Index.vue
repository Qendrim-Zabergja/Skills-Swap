<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold">Messages</h2>
                </div>

                <div class="divide-y">
                    <Link
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        :href="route('messages.show', conversation.user.id)"
                        class="block px-6 py-4 hover:bg-gray-50"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img
                                    :src="conversation.user.profile_photo_url"
                                    :alt="conversation.user.name"
                                    class="w-12 h-12 rounded-full mr-4"
                                />
                                <div>
                                    <h3 class="font-medium">{{ conversation.user.name }}</h3>
                                    <p v-if="conversation.skill_exchange" class="text-sm text-gray-600">
                                        {{ conversation.skill_exchange }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">
                                        {{ conversation.last_message }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ conversation.time }}</p>
                                <div 
                                    v-if="conversation.unread_count > 0"
                                    class="mt-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-gray-800 rounded-full"
                                >
                                    {{ conversation.unread_count }}
                                </div>
                            </div>
                        </div>
                    </Link>

                    <div 
                        v-if="conversations.length === 0" 
                        class="px-6 py-8 text-center text-gray-500"
                    >
                        <p>No messages yet.</p>
                        <Link
                            :href="route('skills.browse')"
                            class="mt-4 inline-flex items-center text-sm text-gray-800 hover:text-gray-900"
                        >
                            Browse skills to start a conversation
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
});
</script>
