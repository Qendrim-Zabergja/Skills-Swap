<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold mb-2">Browse Skills</h1>
            <p class="text-gray-600 mb-8">Find skills to learn or people to teach</p>

            <div class="flex gap-6">
                <!-- Filters Sidebar -->
                <div class="w-1/4 bg-white rounded-lg border p-4 h-fit sticky top-4">
                    <h2 class="text-lg font-semibold mb-4">Filters</h2>

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

                    <!-- Exchange Preferences -->
                    <div class="mb-4">
                        <h3 class="font-medium mb-2">Exchange Preferences</h3>
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
                            <!-- User Info -->
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                                    {{ getInitials(user.name) }}
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ user.location || 'Location not set' }}</p>
                                </div>
                            </div>

                            <!-- Teaching Categories -->
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-700">Skills I Can Teach</h4>
                                    <div class="flex flex-wrap gap-2 relative">
                                        <template v-if="getUniqueCategories(user.teaching_skills).length <= 2">
                                            <span 
                                                v-for="category in getUniqueCategories(user.teaching_skills)"
                                                :key="category"
                                                class="bg-gray-900 text-white px-3 py-1 rounded-full text-xs font-medium"
                                            >
                                                {{ category }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span 
                                                v-for="category in getUniqueCategories(user.teaching_skills).slice(0, 2)"
                                                :key="category"
                                                class="bg-gray-900 text-white px-3 py-1 rounded-full text-xs font-medium"
                                            >
                                                {{ category }}
                                            </span>
                                            <div class="relative inline-block">
                                                <button 
                                                    @click="toggleTeachingCategories(user.id)"
                                                    class="bg-gray-900 text-white px-3 py-1 rounded-full text-xs font-medium cursor-pointer"
                                                >
                                                    +{{ getUniqueCategories(user.teaching_skills).length - 2 }}
                                                </button>
                                                <!-- Dropdown -->
                                                <div 
                                                    v-if="expandedTeachingCategories[user.id]"
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 py-1"
                                                >
                                                    <span 
                                                        v-for="category in getUniqueCategories(user.teaching_skills).slice(2)"
                                                        :key="category"
                                                        class="block px-4 py-2 text-sm text-gray-700"
                                                    >
                                                        {{ category }}
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Skills list -->
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span 
                                        v-for="skill in user.teaching_skills" 
                                        :key="skill.id"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Learning Categories -->
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-700">Skills I Want to Learn</h4>
                                    <div class="flex flex-wrap gap-2 relative">
                                        <template v-if="getUniqueCategories(user.learning_skills).length <= 2">
                                            <span 
                                                v-for="category in getUniqueCategories(user.learning_skills)"
                                                :key="category"
                                                class="bg-gray-700 text-white px-3 py-1 rounded-full text-xs font-medium"
                                            >
                                                {{ category }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span 
                                                v-for="category in getUniqueCategories(user.learning_skills).slice(0, 2)"
                                                :key="category"
                                                class="bg-gray-700 text-white px-3 py-1 rounded-full text-xs font-medium"
                                            >
                                                {{ category }}
                                            </span>
                                            <div class="relative inline-block">
                                                <button 
                                                    @click="toggleLearningCategories(user.id)"
                                                    class="bg-gray-700 text-white px-3 py-1 rounded-full text-xs font-medium cursor-pointer"
                                                >
                                                    +{{ getUniqueCategories(user.learning_skills).length - 2 }}
                                                </button>
                                                <!-- Dropdown -->
                                                <div 
                                                    v-if="expandedLearningCategories[user.id]"
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 py-1"
                                                >
                                                    <span 
                                                        v-for="category in getUniqueCategories(user.learning_skills).slice(2)"
                                                        :key="category"
                                                        class="block px-4 py-2 text-sm text-gray-700"
                                                    >
                                                        {{ category }}
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Skills list -->
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span 
                                        v-for="skill in user.learning_skills" 
                                        :key="skill.id"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-auto pt-4 border-t flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    Member since {{ formatDate(user.created_at) }}
                                </div>
                                <button 
                                    @click="openRequestModal(user)"
                                    class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800"
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

const expandedTeachingCategories = ref({});
const expandedLearningCategories = ref({});

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

const openRequestModal = (user) => {
    console.log('Opening modal for user:', user);
    selectedUser.value = user;
    showRequestModal.value = true;
};

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    });
};

const getUniqueCategories = (skills) => {
    return [...new Set(skills.map(skill => skill.category))].filter(Boolean);
};

const toggleTeachingCategories = (userId) => {
    expandedTeachingCategories.value[userId] = !expandedTeachingCategories.value[userId];
    // Close other dropdowns when opening this one
    if (expandedTeachingCategories.value[userId]) {
        expandedLearningCategories.value[userId] = false;
    }
};

const toggleLearningCategories = (userId) => {
    expandedLearningCategories.value[userId] = !expandedLearningCategories.value[userId];
    // Close other dropdowns when opening this one
    if (expandedLearningCategories.value[userId]) {
        expandedTeachingCategories.value[userId] = false;
    }
};

onMounted(() => {
    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!target.closest('.relative.inline-block')) {
            Object.keys(expandedTeachingCategories.value).forEach(key => {
                expandedTeachingCategories.value[key] = false;
            });
            Object.keys(expandedLearningCategories.value).forEach(key => {
                expandedLearningCategories.value[key] = false;
            });
        }
    });
});
</script>
