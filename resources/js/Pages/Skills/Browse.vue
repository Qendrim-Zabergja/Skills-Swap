<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold mb-2">Browse Skills</h1>
            <p class="text-gray-600 mb-8">Find skills to learn or people to teach</p>

            <!-- Tabs -->
            <div class="mb-6 border-b">
                <div class="flex space-x-4">
                    <button 
                        class="px-6 py-2 font-medium border-b-2 border-black"
                        @click="activeTab = 'skills'"
                        :class="activeTab === 'skills' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    >
                        Skills
                    </button>
                    <button 
                        class="px-6 py-2 font-medium border-b-2 border-transparent"
                        @click="activeTab = 'people'"
                        :class="activeTab === 'people' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    >
                        People
                    </button>
                </div>
            </div>

            <div v-if="activeTab === 'skills'" class="flex gap-6">
                <!-- Filters Sidebar -->
                <div class="w-1/4 bg-white rounded-lg border p-4 h-fit sticky top-4">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="text-lg font-semibold">Filters</h2>
                    </div>

                    <!-- Skill Categories -->
                    <div class="mb-4">
                        <h3 class="font-medium mb-2">Skill Categories</h3>
                        <div class="space-y-2">
                            <label v-for="category in categories" :key="category" class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    :value="category" 
                                    v-model="selectedCategories"
                                    class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                {{ category }}
                            </label>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <h3 class="font-medium mb-2">Location</h3>
                        <div class="relative mb-2">
                            <select class="w-full rounded-md border border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                                <option value="any">Any location</option>
                                <option value="local">Local only</option>
                                <option value="remote">Remote only</option>
                            </select>
                        </div>
                        <input 
                            type="text" 
                            class="w-full rounded-md border border-gray-300 py-2 px-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500" 
                            placeholder="City, state, or country"
                        >
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-4">
                        <h3 class="font-medium mb-2">Rating</h3>
                        <div class="space-y-2">
                            <label v-for="rating in [4, 3, 2, 1]" :key="rating" class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    :value="rating" 
                                    v-model="selectedRatings"
                                    class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <div class="flex items-center">
                                    <span v-for="star in 5" :key="star" class="text-sm"
                                        :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
                                    <span class="ml-1 text-sm text-gray-600">& up</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click="resetFilters" class="px-4 py-2 border rounded hover:bg-gray-50 flex-1">
                            Reset
                        </button>
                        <button @click="applyFilters" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 flex-1">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Search and Sort -->
                    <div class="flex justify-between mb-6">
                        <div class="relative w-full max-w-md">
                            <input 
                                type="text" 
                                v-model="search" 
                                class="w-full pl-10 pr-4 py-2 border rounded-lg" 
                                placeholder="Search skills or keywords..."
                            >
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2 text-sm">Sort by:</span>
                            <div class="relative">
                                <select 
                                    v-model="sortBy" 
                                    @change="handleSortChange"
                                    class="appearance-none pl-3 pr-10 py-2 border rounded-lg bg-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="relevance">Relevance</option>
                                    <option value="rating">Rating</option>
                                    <option value="swaps">Swaps Completed</option>
                                    <option value="name">Name</option>
                                </select>
                                <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Skills Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="user in sortedPaginatedUsers" :key="user.id" class="bg-white rounded-lg border overflow-hidden">
                            <!-- Top section with category badge and rating -->
                            <div class="flex justify-between items-center p-4">
                                <span class="bg-black text-white px-3 py-1 rounded-full text-xs font-medium">
                                    {{ user.teaching_skills[0]?.category || 'Design' }}
                                </span>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                    <span class="ml-1 font-medium">{{ (user.rating || 4.9).toFixed(1) }}</span>
                                </div>
                            </div>
                            
                            <!-- Profile and main content -->
                            <div class="px-4 pb-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-lg">{{ user.teaching_skills[0]?.name || 'Graphic Design & Brand Identity' }}</h3>
                                        <p class="text-gray-600">{{ user.name }}</p>
                                        <div class="flex items-center text-gray-500 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span class="text-sm">{{ user.location || 'Location not specified' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <p class="mt-4 text-gray-800">
                                    {{ user.teaching_skills[0]?.description || 'I can teach you how to create stunning logos, brand identities, and marketing materials using Adobe Creative Suite.' }}
                                </p>
                                
                                <!-- Looking to learn section -->
                                <div class="mt-4">
                                    <p class="text-sm font-medium mb-2">Looking to learn:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span 
                                            v-for="skill in user.learning_skills" 
                                            :key="skill.id"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white border border-gray-200"
                                        >
                                            {{ skill.name }}
                                        </span>
                                        <span v-if="user.learning_skills.length === 0" class="text-sm text-gray-500">
                                            No learning skills listed
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="border-t p-4 flex justify-between items-center">
                                <span class="text-sm text-gray-500">{{ user.swaps_completed || 0 }} swaps completed</span>
                                <button 
                                    @click="openRequestModal(user)"
                                    class="px-4 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800"
                                >
                                    Request Swap
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-center gap-2">
                        <button 
                            class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50"
                            :disabled="currentPage === 1"
                            @click="currentPage--"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            v-for="page in totalPages" 
                            :key="page"
                            @click="currentPage = page"
                            :class="[
                                'px-3 py-1 border rounded hover:bg-gray-50',
                                currentPage === page ? 'bg-black text-white' : ''
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button 
                            class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50"
                            :disabled="currentPage === totalPages"
                            @click="currentPage++"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else-if="activeTab === 'people'" class="p-8 text-center text-gray-500">
                People view is not implemented in this example
            </div>
        </div>
    </div>

    <SkillExchangeModal 
        :show="showRequestModal" 
        :user="auth.user" 
        :recipient="selectedUser" 
        @close="showRequestModal = false"
    />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import SkillExchangeModal from '@/Components/SkillExchangeModal.vue';

const props = defineProps({
    users: Array,
    auth: Object,
});

const showRequestModal = ref(false);
const selectedUser = ref(null);
const activeTab = ref('skills');
const sortBy = ref('relevance');
const sortDirection = ref('desc');

const categories = [
    'Design',
    'Development',
    'Music',
    'Language',
    'Business',
    'Lifestyle',
    'Photography',
    'Education'
];

const search = ref('');
const selectedCategories = ref([]);
const selectedRatings = ref([]);
const currentPage = ref(1);
const itemsPerPage = 10;

const filters = ref({
    lookingForMySkills: false,
    offeringSkillsIWant: false,
    perfectMatchesOnly: false
});

const resetFilters = () => {
    selectedCategories.value = [];
    selectedRatings.value = [];
    filters.value = {
        lookingForMySkills: false,
        offeringSkillsIWant: false,
        perfectMatchesOnly: false
    };
    search.value = '';
    currentPage.value = 1;
    sortBy.value = 'relevance';
};

const applyFilters = () => {
    currentPage.value = 1;
};

const handleSortChange = () => {
    console.log('Sorting by:', sortBy.value);
    currentPage.value = 1;
};

const filteredUsers = computed(() => {
    let filtered = props.users || [];

    // Search filter
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        filtered = filtered.filter(user => {
            const mainSkill = user.teaching_skills[0];
            return user.name.toLowerCase().includes(searchLower) ||
                user.surname?.toLowerCase().includes(searchLower) ||
                mainSkill?.name.toLowerCase().includes(searchLower) ||
                mainSkill?.description?.toLowerCase().includes(searchLower) ||
                user.learning_skills.some(skill => 
                    skill.name.toLowerCase().includes(searchLower)
                );
        });
    }

    // Category filter
    if (selectedCategories.value.length > 0) {
        filtered = filtered.filter(user => {
            const mainSkillCategory = user.teaching_skills[0]?.category;
            return selectedCategories.value.includes(mainSkillCategory) ||
                user.learning_skills.some(skill => selectedCategories.value.includes(skill.category));
        });
    }

    // Rating filter
    if (selectedRatings.value.length > 0) {
        filtered = filtered.filter(user =>
            selectedRatings.value.some(rating => (user.rating || 0) >= rating)
        );
    }

    // Exchange preferences filters
    if (filters.value.lookingForMySkills) {
        // Implementation depends on your data structure
    }

    if (filters.value.offeringSkillsIWant) {
        // Implementation depends on your data structure
    }

    if (filters.value.perfectMatchesOnly) {
        // Implementation depends on your data structure
    }

    return filtered;
});

const sortedUsers = computed(() => {
    let sorted = [...filteredUsers.value];
    
    switch (sortBy.value) {
        case 'rating':
            sorted.sort((a, b) => (b.rating || 0) - (a.rating || 0));
            break;
        case 'swaps':
            sorted.sort((a, b) => (b.swaps_completed || 0) - (a.swaps_completed || 0));
            break;
        case 'name':
            sorted.sort((a, b) => {
                const nameA = a.name || '';
                const nameB = b.name || '';
                return nameA.localeCompare(nameB);
            });
            break;
        case 'relevance':
        default:
            // For relevance, we might want to keep the original order or implement a more complex algorithm
            break;
    }
    
    return sorted;
});

const sortedPaginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return sortedUsers.value.slice(start, end);
});

const totalPages = computed(() => 
    Math.max(1, Math.ceil(filteredUsers.value.length / itemsPerPage))
);

const openRequestModal = (user) => {
    console.log('Opening modal for user:', user);
    selectedUser.value = user;
    showRequestModal.value = true;
};

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

onMounted(() => {
    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!target.closest('.dropdown-container')) {
            // Close any open dropdowns if needed
        }
    });
});
</script>