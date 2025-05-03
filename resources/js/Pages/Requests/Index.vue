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
                            <div v-for="request in localIncomingRequests" :key="request.id" class="flex items-center justify-between p-4 bg-white border rounded-lg">
                              <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                                  {{ getInitials(request.user.name) }}
                                </div>
                                <div>
                                  <div class="text-sm font-medium text-gray-900">{{ request.user.name }}</div>
                                  <div class="text-sm text-gray-500">
                                    Wants to learn: {{ request.skill_wanted }}
                                    <br />
                                    Offers to teach: {{ request.skill_offered }}
                                  </div>
                                  <div class="text-xs text-gray-400 mt-1">
                                    Received {{ formatDate(request.created_at) }}
                                  </div>
                                  <div v-if="request.message" class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                    {{ request.message }}
                                  </div>
                                </div>
                              </div>
                              <div class="flex items-center space-x-2">
                                <template v-if="request.status === 'Pending'">
                                  <button
                                    @click="acceptRequest(request.id)"
                                    class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200"
                                  >
                                    Accept
                                  </button>
                                  <button
                                    @click="declineRequest(request.id)"
                                    class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200"
                                  >
                                    Decline
                                  </button>
                                </template>
                                <template v-else-if="request.status === 'Accepted'">
                                  <Link
                                    :href="route('messages.show', request.user.id)"
                                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200"
                                  >
                                    Message
                                  </Link>
                                  <div class="text-sm text-green-600">Accepted</div>
                                </template>
                                <template v-else>
                                  <div class="text-sm" :class="{
                                    'text-red-600': request.status === 'Declined',
                                    'text-gray-600': request.status === 'Cancelled'
                                  }">
                                    {{ request.status }}
                                  </div>
                                </template>
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
                            <div v-for="request in localOutgoingRequests" :key="request.id" class="group relative p-4 bg-white border rounded-lg">
                              <button 
                                v-if="request.status === 'Pending'"
                                @click="cancelRequest(request.id)" 
                                class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-gray-700 bg-gray-100 rounded-full shadow-sm z-50 hover:bg-gray-200"
                              >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                              </button>
                              <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                                  {{ getInitials(request.recipient.name) }}
                                </div>
                                <div>
                                  <div class="text-sm font-medium text-gray-900">{{ request.recipient.name }}</div>
                                  <div class="text-sm text-gray-500">
                                    You want to learn: {{ request.skill_wanted }}
                                    <br />
                                    You offer to teach: {{ request.skill_offered }}
                                  </div>
                                  <div class="text-xs text-gray-400 mt-1">
                                    Sent {{ formatDate(request.created_at) }}
                                  </div>
                                  <div v-if="request.message" class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                    {{ request.message }}
                                  </div>
                                </div>
                              </div>
                              <div class="mt-2 text-sm" :class="{
                                'text-yellow-600': request.status === 'Pending',
                                'text-green-600': request.status === 'Accepted',
                                'text-red-600': request.status === 'Declined',
                                'text-gray-600': request.status === 'Cancelled'
                              }">
                                {{ request.status }}
                                <Link
                                  v-if="request.status === 'Accepted'"
                                  :href="route('messages.show', request.recipient.id)"
                                  class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200"
                                >
                                  Message
                                </Link>
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

const acceptRequest = (id) => {
    router.post(route('requests.accept', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the incoming list
            localIncomingRequests.value = localIncomingRequests.value.filter(r => r.id !== id);
            alert('Request accepted successfully!');
        }
    });
};

const declineRequest = (id) => {
    router.post(route('requests.decline', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the incoming list
            localIncomingRequests.value = localIncomingRequests.value.filter(r => r.id !== id);
            alert('Request declined successfully!');
        }
    });
};

const cancelRequest = (id) => {
    router.post(route('requests.cancel', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the request from the outgoing list
            localOutgoingRequests.value = localOutgoingRequests.value.filter(r => r.id !== id);
            alert('Request cancelled successfully!');
        }
    });
};

const getInitials = (name) => {
    const names = name.split(' ');
    return names[0][0] + names[names.length - 1][0];
};

const formatDate = (date) => {
    const d = new Date(date);
    return d.toLocaleDateString();
};
</script>
