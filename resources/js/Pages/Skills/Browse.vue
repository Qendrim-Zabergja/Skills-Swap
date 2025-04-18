<template>
    <Head title="Browse Skills" />

    <div class="min-h-screen bg-white">
        <Navbar />
        
        <div class="container mx-auto px-4 py-8">
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
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Design" class="mr-2">
                                Design
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Development" class="mr-2">
                                Development
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Music" class="mr-2">
                                Music
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Language" class="mr-2">
                                Language
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Business" class="mr-2">
                                Business
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Lifestyle" class="mr-2">
                                Lifestyle
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Photography" class="mr-2">
                                Photography
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.categories" value="Education" class="mr-2">
                                Education
                            </label>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-6">
                        <h3 class="font-medium mb-3">Rating</h3>
                        <div class="space-y-2">
                            <label v-for="rating in [4, 3, 2, 1]" :key="rating" class="flex items-center">
                                <input type="checkbox" v-model="filters.ratings" :value="rating" class="mr-2">
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
                                <input type="checkbox" v-model="filters.lookingForMySkills" class="mr-2">
                                Looking for my skills
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.offeringSkillsIWant" class="mr-2">
                                Offering skills I want
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="filters.perfectMatchesOnly" class="mr-2">
                                Perfect matches only
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click="resetFilters" class="px-4 py-2 border rounded hover:bg-gray-50">Reset</button>
                        <button @click="applyFilters" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">Apply
                            Filters</button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" v-model="searchQuery" @input="handleSearch"
                                class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="Search skills or keywords...">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Skills Grid -->
                    <div v-if="loading" class="text-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-black mx-auto"></div>
                    </div>

                    <div v-else-if="skills.length === 0" class="text-center py-12">
                        <p class="text-gray-500">No skills found matching your criteria.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="skill in skills" :key="skill.id"
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-gray-500 text-lg">{{ skill.user?.name?.charAt(0) || '?' }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium">{{ skill.title }}</h3>
                                        <p class="text-sm text-gray-500">{{ skill.user?.name }}</p>
                                        <div class="flex items-center mt-1">
                                            <div class="flex items-center">
                                                <span v-for="i in 5" :key="i" class="text-sm"
                                                    :class="i <= skill.rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
                                                <span class="text-sm text-gray-500 ml-1">{{ skill.rating }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">{{ skill.description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ skill.category }}</span>
                                <button @click="requestSwap(skill)"
                                    class="px-3 py-1 bg-black text-white text-sm rounded hover:bg-gray-800">
                                    Request Swap
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="skills.length > 0" class="mt-6 flex justify-center gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button v-for="page in paginationRange" :key="page" @click="goToPage(page)"
                            :class="['px-3 py-1 border rounded hover:bg-gray-50', currentPage === page ? 'bg-black text-white' : '']">
                            {{ page }}
                        </button>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="p-2 border rounded hover:bg-gray-50 disabled:opacity-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import debounce from 'lodash/debounce';
import axios from 'axios';

export default {
    components: {
        Head,
        Navbar
    },

    data() {
        return {
            searchQuery: '',
            filters: {
                categories: [],
                ratings: [],
                lookingForMySkills: false,
                offeringSkillsIWant: false,
                perfectMatchesOnly: false,
            },
            skills: [],
            currentPage: 1,
            totalPages: 1,
            loading: false,
        }
    },

    computed: {
        paginationRange() {
            const range = [];
            for (let i = 1; i <= this.totalPages; i++) {
                range.push(i);
            }
            return range;
        },
    },

    methods: {
        handleSearch: debounce(function () {
            this.currentPage = 1;
            this.fetchSkills();
        }, 300),

        async fetchSkills() {
            this.loading = true;
            try {
                const response = await axios.get('/api/skills', {
                    params: {
                        page: this.currentPage,
                        search: this.searchQuery,
                        categories: this.filters.categories,
                        ratings: this.filters.ratings,
                        lookingForMySkills: this.filters.lookingForMySkills,
                        offeringSkillsIWant: this.filters.offeringSkillsIWant,
                        perfectMatchesOnly: this.filters.perfectMatchesOnly
                    }
                });
                
                this.skills = response.data.data;
                this.totalPages = Math.ceil(response.data.total / response.data.per_page);
            } catch (error) {
                console.error('Failed to fetch skills:', error);
                this.skills = [];
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                categories: [],
                ratings: [],
                lookingForMySkills: false,
                offeringSkillsIWant: false,
                perfectMatchesOnly: false,
            };
            this.searchQuery = '';
            this.currentPage = 1;
            this.fetchSkills();
        },

        applyFilters() {
            this.currentPage = 1;
            this.fetchSkills();
        },

        async requestSwap(skill) {
            try {
                await axios.post('/api/requests', {
                    skill_id: skill.id,
                });
                // Show success message
                alert('Swap request sent successfully!');
            } catch (error) {
                console.error('Failed to request swap:', error);
                alert('Failed to send swap request. Please try again.');
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.fetchSkills();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.fetchSkills();
            }
        },

        goToPage(page) {
            this.currentPage = page;
            this.fetchSkills();
        }
    },

    mounted() {
        this.fetchSkills();
    }
}
</script>
