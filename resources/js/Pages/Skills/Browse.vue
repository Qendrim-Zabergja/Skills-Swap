<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold mb-2">Browse Skills</h1>
            <p class="text-gray-600 mb-8">Find skills to learn or people to teach</p>

            <div class="flex gap-6">
                <!-- Filters Sidebar -->
                <div class="w-1/4 bg-white rounded-lg border p-6">
                    <h2 class="text-lg font-semibold mb-6">Filters</h2>

                    <!-- Skill Categories -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Skill Categories</h3>
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

                    <!-- Rating Filter -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Rating</h3>
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

                    <!-- Exchange Preferences -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Exchange Preferences</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="filters.lookingForMySkills"
                                    class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                Looking for my skills
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="filters.offeringSkillsIWant"
                                    class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                Offering skills I want
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="filters.perfectMatchesOnly"
                                    class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                Perfect matches only
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click="resetFilters" class="px-4 py-2 border rounded hover:bg-gray-50">
                            Reset
                        </button>
                        <button @click="applyFilters" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <div class="relative">
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
                    </div>

                    <!-- Skills Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="user in paginatedUsers" :key="user.id" class="bg-white rounded-lg border p-6 flex flex-col h-[400px]">
                            <!-- Category and Rating -->
                            <div class="flex justify-between items-center">
                                <span class="bg-gray-900 text-white px-3 py-1 rounded-full text-sm">
                                    {{ user.teaching_skills[0]?.category }}
                                </span>
                                <div class="flex items-center">
                                    <span class="text-yellow-400">★</span>
                                    <span class="ml-1">{{ user.rating }}</span>
                                </div>
                            </div>

                            <!-- Main Content -->
                            <div class="flex-grow">
                                <!-- Main Skill and Description -->
                                <h3 class="text-xl font-semibold mt-4 mb-1">{{ user.teaching_skills[0]?.name }}</h3>
                                
                                <!-- User Info -->
                                <div class="flex items-center mb-3">
                                    <img :src="user.profile_photo" :alt="`${user.name}`" class="w-8 h-8 rounded-full object-cover">
                                    <div class="ml-2">
                                        <div class="font-medium">{{ user.name }}</div>
                                        <div class="text-gray-500 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ user.location }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Skill Description -->
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ user.teaching_skills[0]?.description }}</p>

                                <!-- Looking to Learn Section -->
                                <div>
                                    <div class="text-sm text-gray-600 mb-2">Looking to learn:</div>
                                    <div class="flex flex-wrap gap-2">
                                        <span 
                                            v-for="skill in user.learning_skills.slice(0, 3)" 
                                            :key="skill.id"
                                            class="bg-gray-100 px-3 py-1 rounded-full text-sm"
                                        >
                                            {{ skill.name }}
                                        </span>
                                        <span v-if="user.learning_skills.length > 3" class="text-sm text-gray-500">
                                            +{{ user.learning_skills.length - 3 }} more
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats and Action -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t">
                                <div class="text-sm text-gray-500">
                                    {{ user.swaps_completed }} swaps completed
                                </div>
                                <button @click="requestSwap(user)" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 text-sm">
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
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    users: {
        type: Array,
        required: true
    }
});

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
};

const applyFilters = () => {
    currentPage.value = 1;
};

const filteredUsers = computed(() => {
    let filtered = props.users;

    // Search filter
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        filtered = filtered.filter(user => {
            const mainSkill = user.teaching_skills[0];
            return user.name.toLowerCase().includes(searchLower) ||
                user.surname?.toLowerCase().includes(searchLower) ||
                mainSkill?.name.toLowerCase().includes(searchLower) ||
                mainSkill?.description.toLowerCase().includes(searchLower) ||
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
            selectedRatings.value.some(rating => user.rating >= rating)
        );
    }

    // Exchange preferences filters
    if (filters.value.lookingForMySkills) {
        // TODO: Implement when user's skills are available
        filtered = filtered.filter(user => 
            user.learning_skills.some(skill => currentUserTeachingSkills.includes(skill.name))
        );
    }

    if (filters.value.offeringSkillsIWant) {
        // TODO: Implement when user's skills are available
        filtered = filtered.filter(user => 
            user.teaching_skills.some(skill => currentUserLearningSkills.includes(skill.name))
        );
    }

    if (filters.value.perfectMatchesOnly) {
        // TODO: Implement when user's skills are available
        filtered = filtered.filter(user => 
            user.learning_skills.some(skill => currentUserTeachingSkills.includes(skill.name)) &&
            user.teaching_skills.some(skill => currentUserLearningSkills.includes(skill.name))
        );
    }

    return filtered;
});

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredUsers.value.slice(start, end);
});

const totalPages = computed(() => 
    Math.ceil(filteredUsers.value.length / itemsPerPage)
);

const requestSwap = (user) => {
    // TODO: Implement swap request functionality
    console.log('Requesting swap with user:', user.id);
};
</script>
