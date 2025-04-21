<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue';

const props = defineProps({
    auth: Object,
    skills: Array,
    incomingRequests: Array,
    outgoingRequests: Array,
    messages: Array,
});

const activeTab = ref('edit-profile');
const photoInput = ref(null);

const form = useForm({
    name: props.auth.user.name,
    email: props.auth.user.email,
    phone: props.auth.user.phone || '',
});

const updateProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const acceptRequest = (requestId) => {
    router.post(route('requests.accept', requestId));
};

const declineRequest = (requestId) => {
    router.post(route('requests.decline', requestId));
};

const cancelRequest = (requestId) => {
    router.post(route('requests.cancel', requestId));
};

const updatePhoto = (event) => {
    if (event.target.files.length > 0) {
        const formData = new FormData();
        formData.append('photo', event.target.files[0]);
        
        router.post(route('profile.photo.update'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                event.target.value = '';
            },
        });
    }
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Left Column - Profile Info -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex flex-col items-center">
                            <div class="relative w-32 h-32 mb-4">
                                <img
                                    :src="auth.user.profile_photo_url || '/default-avatar.png'"
                                    class="w-full h-full rounded-full object-cover"
                                    alt="Profile"
                                />
                                <button
                                    class="absolute bottom-0 right-0 p-1 bg-gray-800 rounded-full text-white"
                                    @click="$refs.photoInput.click()"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <input
                                    ref="photoInput"
                                    type="file"
                                    class="hidden"
                                    @change="updatePhoto"
                                    accept="image/*"
                                />
                            </div>
                            <h2 class="text-2xl font-semibold">{{ auth.user.name }}</h2>
                            <p class="text-gray-600">Web Developer & Designer</p>
                            <button
                                @click="$refs.photoInput.click()"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Change Photo
                            </button>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <h3 class="font-medium">Location</h3>
                                    <p class="text-gray-600">{{ auth.user.location || 'Not specified' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <h3 class="font-medium">Email</h3>
                                    <p class="text-gray-600">{{ auth.user.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <h3 class="font-medium">Phone</h3>
                                    <p class="text-gray-600">{{ auth.user.phone || 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="font-semibold mb-4">Skills I Can Teach</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in skills.filter(s => s.type === 'teach')"
                                    :key="skill.id"
                                    class="px-3 py-1 bg-gray-100 rounded-full text-sm"
                                >
                                    {{ skill.name }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="font-semibold mb-4">Skills I Want to Learn</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in skills.filter(s => s.type === 'learn')"
                                    :key="skill.id"
                                    class="px-3 py-1 bg-gray-100 rounded-full text-sm"
                                >
                                    {{ skill.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Tabs -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white rounded-lg shadow">
                        <div class="border-b">
                            <nav class="flex">
                                <button
                                    @click="activeTab = 'swap-requests'"
                                    :class="[
                                        'px-6 py-4 text-sm font-medium',
                                        activeTab === 'swap-requests'
                                            ? 'border-b-2 border-gray-800 text-gray-800'
                                            : 'text-gray-500 hover:text-gray-700'
                                    ]"
                                >
                                    Swap Requests
                                </button>
                                <button
                                    @click="activeTab = 'messages'"
                                    :class="[
                                        'px-6 py-4 text-sm font-medium',
                                        activeTab === 'messages'
                                            ? 'border-b-2 border-gray-800 text-gray-800'
                                            : 'text-gray-500 hover:text-gray-700'
                                    ]"
                                >
                                    Messages
                                </button>
                                <button
                                    @click="activeTab = 'edit-profile'"
                                    :class="[
                                        'px-6 py-4 text-sm font-medium',
                                        activeTab === 'edit-profile'
                                            ? 'border-b-2 border-gray-800 text-gray-800'
                                            : 'text-gray-500 hover:text-gray-700'
                                    ]"
                                >
                                    Edit Profile
                                </button>
                            </nav>
                        </div>

                        <!-- Swap Requests Tab -->
                        <div v-if="activeTab === 'swap-requests'" class="p-6">
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold mb-4">Incoming Requests</h3>
                                <div class="space-y-4">
                                    <div
                                        v-for="request in incomingRequests"
                                        :key="request.id"
                                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                                    >
                                        <div>
                                            <div class="flex items-center">
                                                <img
                                                    :src="request.user.profile_photo_url"
                                                    class="w-10 h-10 rounded-full mr-3"
                                                    :alt="request.user.name"
                                                />
                                                <div>
                                                    <h4 class="font-medium">{{ request.user.name }}</h4>
                                                    <div class="text-sm text-gray-600">
                                                        <p>Wants to learn: {{ request.skill_wanted }}</p>
                                                        <p>Offers to teach: {{ request.skill_offered }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('messages.show', request.user.id)"
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                                            >
                                                Message
                                            </Link>
                                            <button
                                                @click="acceptRequest(request.id)"
                                                class="px-3 py-1 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700"
                                            >
                                                Accept
                                            </button>
                                            <button
                                                @click="declineRequest(request.id)"
                                                class="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                                            >
                                                Decline
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-4">Outgoing Requests</h3>
                                <div class="space-y-4">
                                    <div
                                        v-for="request in outgoingRequests"
                                        :key="request.id"
                                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                                    >
                                        <div class="flex items-center">
                                            <img
                                                :src="request.recipient.profile_photo_url"
                                                class="w-10 h-10 rounded-full mr-3"
                                                :alt="request.recipient.name"
                                            />
                                            <div>
                                                <h4 class="font-medium">{{ request.recipient.name }}</h4>
                                                <div class="text-sm text-gray-600">
                                                    <p>You want to learn: {{ request.skill_wanted }}</p>
                                                    <p>You offer to teach: {{ request.skill_offered }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('messages.show', request.recipient.id)"
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                                            >
                                                Message
                                            </Link>
                                            <button
                                                @click="cancelRequest(request.id)"
                                                class="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Tab -->
                        <div v-if="activeTab === 'messages'" class="p-6">
                            <div class="space-y-4">
                                <Link
                                    v-for="message in messages"
                                    :key="message.id"
                                    :href="route('messages.show', message.user.id)"
                                    class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img
                                                :src="message.user.profile_photo_url"
                                                class="w-10 h-10 rounded-full mr-3"
                                                :alt="message.user.name"
                                            />
                                            <div>
                                                <h4 class="font-medium">{{ message.user.name }}</h4>
                                                <p class="text-sm text-gray-600">
                                                    {{ message.skill_exchange }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ message.time }}</p>
                                            <p class="text-sm mt-1 line-clamp-1">
                                                {{ message.last_message }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Edit Profile Tab -->
                        <div v-if="activeTab === 'edit-profile'" class="p-6">
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                    />
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                    />
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                    />
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                                        :disabled="form.processing"
                                    >
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
