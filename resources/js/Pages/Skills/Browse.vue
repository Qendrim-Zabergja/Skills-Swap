<template>
    <Head title="Browse Skills" />
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('skills.browse')" :active="route().current('skills.browse')">
                                Browse Skills
                            </NavLink>
                            <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                Profile
                            </NavLink>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            {{ $page.props.auth.user.name }}
                                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold mb-2">Browse Skills</h1>
            <p class="text-gray-600 mb-8">Find skills to learn or people to teach</p>

            <div class="flex gap-6">
                <!-- Filters Sidebar -->
                <div class="w-1/4 bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-6">Filters</h2>

                    <!-- Skill Type -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Skill Type</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    v-model="filters.type"
                                    value="teach"
                                    class="mr-2 rounded-full border-gray-300 text-gray-800 focus:ring-gray-800"
                                />
                                Teaching Skills
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    v-model="filters.type"
                                    value="learn"
                                    class="mr-2 rounded-full border-gray-300 text-gray-800 focus:ring-gray-800"
                                />
                                Learning Skills
                            </label>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Search</h3>
                        <input 
                            type="text" 
                            v-model="search"
                            placeholder="Search skills..."
                            class="w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                        />
                    </div>

                    <div class="flex gap-2">
                        <button @click="resetFilters" class="px-4 py-2 border rounded hover:bg-gray-50">
                            Reset
                        </button>
                        <button @click="applyFilters" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Skills Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="skill in paginatedSkills" :key="skill.id" class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <img
                                        :src="skill.user.profile_photo_url"
                                        :alt="skill.user.name"
                                        class="w-10 h-10 rounded-full mr-3"
                                    />
                                    <div>
                                        <h3 class="font-medium">{{ skill.user.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ skill.type === 'teach' ? 'Teaching' : 'Learning' }}</p>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-lg font-semibold mb-2">{{ skill.name }}</h4>
                            <p class="text-gray-600 mb-4 line-clamp-3">{{ skill.description }}</p>

                            <div class="flex justify-end">
                                <button 
                                    @click="requestSwap(skill)"
                                    class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
                                >
                                    Request Swap
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1" class="mt-6 flex justify-center gap-2">
                        <button 
                            @click="currentPage--"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 border rounded disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <span class="px-3 py-1">Page {{ currentPage }} of {{ totalPages }}</span>
                        <button 
                            @click="currentPage++"
                            :disabled="currentPage === totalPages"
                            class="px-3 py-1 border rounded disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    skills: {
        type: Array,
        required: true
    }
});

const search = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const filters = ref({
    type: 'teach'
});

const resetFilters = () => {
    filters.value = {
        type: 'teach'
    };
    search.value = '';
    currentPage.value = 1;
};

const applyFilters = () => {
    currentPage.value = 1;
};

const filteredSkills = computed(() => {
    let filtered = props.skills;

    // Type filter
    if (filters.value.type) {
        filtered = filtered.filter(skill => skill.type === filters.value.type);
    }

    // Search filter
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        filtered = filtered.filter(skill => 
            skill.name.toLowerCase().includes(searchLower) ||
            skill.description?.toLowerCase().includes(searchLower) ||
            skill.user.name.toLowerCase().includes(searchLower)
        );
    }

    return filtered;
});

const paginatedSkills = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredSkills.value.slice(start, end);
});

const totalPages = computed(() => 
    Math.ceil(filteredSkills.value.length / itemsPerPage)
);

const requestSwap = (skill) => {
    // TODO: Implement swap request functionality
    console.log('Requesting swap for skill:', skill.id);
};
</script>
