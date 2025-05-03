<template>
    <div class="min-h-screen bg-gray-100">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="mb-4 border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button 
                            v-for="tab in ['Swap Requests', 'Messages']" 
                            :key="tab"
                            :class="[
                                selectedTab === tab
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm'
                            ]"
                            @click="selectedTab = tab"
                        >
                            {{ tab }}
                        </button>
                    </nav>
                </div>

                <!-- Incoming Requests -->
                <div v-if="selectedTab === 'Swap Requests'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold mb-4">Incoming Requests</h2>
                        <div v-if="localIncomingRequests.length === 0" class="text-gray-500">
                            No incoming requests at the moment.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="request in localIncomingRequests" :key="request.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="flex items-center">
                                        <img :src="request.user.profile_photo_url" :alt="request.user.name" class="h-10 w-10 rounded-full">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ request.user.name }}</p>
                                            <div class="text-sm text-gray-500">
                                                <p>Wants to learn: {{ request.skill_wanted }}</p>
                                                <p>Offers to teach: {{ request.skill_offered }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button @click="message(request.user)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Message
                                    </button>
                                    <button @click="accept(request)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                                        Accept
                                    </button>
                                    <button @click="decline(request)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                                        Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outgoing Requests -->
                <div v-if="selectedTab === 'Swap Requests'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold mb-4">Outgoing Requests</h2>
                        <div v-if="localOutgoingRequests.length === 0" class="text-gray-500">
                            You haven't sent any requests yet.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="request in localOutgoingRequests" :key="request.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="flex items-center">
                                        <img :src="request.recipient.profile_photo_url" :alt="request.recipient.name" class="h-10 w-10 rounded-full">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ request.recipient.name }}</p>
                                            <div class="text-sm text-gray-500">
                                                <p>You want to learn: {{ request.skill_wanted }}</p>
                                                <p>You offer to teach: {{ request.skill_offered }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button @click="message(request.recipient)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Message
                                    </button>
                                    <button @click="cancel(request)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    incomingRequests: {
        type: Array,
        required: true
    },
    outgoingRequests: {
        type: Array,
        required: true
    }
});

const selectedTab = ref('Swap Requests');
const localIncomingRequests = ref([...props.incomingRequests]);
const localOutgoingRequests = ref([...props.outgoingRequests]);

const message = (user) => {
    router.visit(route('messages.show', user.id));
};

const accept = (request) => {
    router.post(route('requests.accept', request.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the incoming list
            localIncomingRequests.value = localIncomingRequests.value.filter(r => r.id !== request.id);
            alert('Request accepted successfully!');
        }
    });
};

const decline = (request) => {
    router.post(route('requests.decline', request.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the incoming list
            localIncomingRequests.value = localIncomingRequests.value.filter(r => r.id !== request.id);
            alert('Request declined successfully!');
        }
    });
};

const cancel = (request) => {
    router.post(route('requests.cancel', request.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the outgoing list
            localOutgoingRequests.value = localOutgoingRequests.value.filter(r => r.id !== request.id);
            alert('Request cancelled successfully!');
        }
    });
};
</script>
