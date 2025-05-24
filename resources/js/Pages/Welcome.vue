<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-white">
        <!-- Header -->
        <Navbar />

        <main>
            <!-- Hero Section -->
            <section class="py-24 px-4 max-w-full bg-gray-100 mx-auto">
                <div class="text-center mb-20">
                    <h1 class="text-5xl font-bold mb-3">Swap Skills, Learn & Teach for Free!</h1>
                    <p class="text-gray-600 mb-10">Exchange your expertise with others. No money involved - just knowledge sharing.</p>
                    
                    <!-- Search Section -->
                    <div class="max-w-xl mx-auto">
                        <div class="flex items-center gap-2 relative">
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    v-model="searchQuery" 
                                    @input="updateSearch"
                                    @focus="showSuggestions = searchQuery.length > 0"
                                    @blur="setTimeout(() => { showSuggestions = false }, 200)"
                                    @keyup.enter="searchSkills"
                                    placeholder="Search skills (e.g., Graphic Design)" 
                                    class="w-full px-4 py-2 rounded border border-gray-200 focus:outline-none focus:border-gray-300 bg-white"
                                    autocomplete="off"
                                />
                                
                                <!-- Suggestions Dropdown -->
                                <div 
                                    v-if="showSuggestions && searchSuggestions.length > 0"
                                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"
                                >
                                    <div 
                                        v-for="(suggestion, index) in searchSuggestions" 
                                        :key="index"
                                        @mousedown="selectSuggestion(suggestion)"
                                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                    >
                                        {{ suggestion }}
                                    </div>
                                </div>
                            </div>
                            <button 
                                @click="searchSkills" 
                                class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800 whitespace-nowrap"
                            >
                                Find Skills
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Users Section -->
            <section class="py-8 px-4 max-w-6xl mx-auto">
                <h2 class="text-xl font-semibold mb-8">Featured Skill Swappers</h2>
                
                <div v-if="featuredUsers.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div v-for="user in featuredUsers" :key="user.id" class="bg-white rounded-lg p-6 border border-gray-100">
                        <!-- User Info Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <img 
                                            v-if="user.profile_photo_url && !imageLoadErrors.has(user.id)" 
                                            :src="user.profile_photo_url"
                                            :alt="user.name" 
                                            class="w-full h-full object-cover" 
                                            @error="handleImageError(user.id)"
                                        />
                                        <span v-else class="text-gray-500 text-sm">
                                            {{ getInitials(user.name) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ user.location || 'Location not specified' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    <span v-for="i in Math.min(5, Math.floor(user.rating || 0))" :key="i" class="text-sm">★</span>
                                    <span v-if="user.rating % 1 > 0.3 && user.rating % 1 < 0.8" class="text-sm">½</span>
                                    <span v-for="i in (5 - Math.ceil(user.rating || 0))" :key="i+5" class="text-gray-300 text-sm">★</span>
                                </div>
                                <span class="ml-1 text-gray-600 text-sm">{{ user.rating ? user.rating.toFixed(1) : 'New' }}</span>
                            </div>
                        </div>

                        <!-- Teaching Skills -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Can teach:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="skill in user.teaching_skills.slice(0, 2)" 
                                    :key="skill.id"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ skill.name }}
                                </span>
                                <span v-if="user.teaching_skills.length > 2" class="text-xs text-gray-500">
                                    +{{ user.teaching_skills.length - 2 }} more
                                </span>
                                <span v-if="user.teaching_skills.length === 0" class="text-xs text-gray-500">
                                    No teaching skills listed
                                </span>
                            </div>
                        </div>

                        <!-- Learning Skills -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Looking to learn:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="skill in user.learning_skills.slice(0, 2)" 
                                    :key="skill.id"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                >
                                    {{ skill.name }}
                                </span>
                                <span v-if="user.learning_skills.length > 2" class="text-xs text-gray-500">
                                    +{{ user.learning_skills.length - 2 }} more
                                </span>
                                <span v-if="user.learning_skills.length === 0" class="text-xs text-gray-500">
                                    No learning skills listed
                                </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500">
                                {{ user.swaps_completed || 0 }} swaps completed
                            </div>
                            <button 
                                @click="requestSwap(user.id)"
                                class="bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-800"
                            >
                                Request Swap
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-10 bg-gray-50 rounded-lg mb-8">
                    <p class="text-gray-500">No featured users available yet. Be the first to share your expertise!</p>
                    <Link v-if="$page.props.auth.user" :href="route('profile.edit')"
                        class="bg-black text-white px-4 py-2 rounded mt-4 inline-block">Add Your Skills</Link>
                    <Link v-else :href="route('register')"
                        class="bg-black text-white px-4 py-2 rounded mt-4 inline-block">Sign Up to Share</Link>
                </div>

                <!-- View All Skills link -->
                <div class="text-center">
                    <Link :href="route('skills.browse')"
                        class="bg-white border border-gray-200 text-black px-4 py-2 rounded hover:border-gray-300 hover:text-gray-900">View All Skills</Link>
                </div>
            </section>

            <!-- How It Works Section -->
            <section class="py-16 px-4 max-w-6xl mx-auto">
                <h2 class="text-2xl font-semibold text-center mb-12">How SkillSwap Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <span class="font-semibold text-lg">1</span>
                        </div>
                        <h3 class="font-semibold text-lg mb-3">List Your Skills</h3>
                        <p class="text-gray-600">Create a profile and list the skills you can teach others.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <span class="font-semibold text-lg">2</span>
                        </div>
                        <h3 class="font-semibold text-lg mb-3">Find & Connect</h3>
                        <p class="text-gray-600">Browse skills you want to learn and connect with potential teachers.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <span class="font-semibold text-lg">3</span>
                        </div>
                        <h3 class="font-semibold text-lg mb-3">Teach & Learn</h3>
                        <p class="text-gray-600">Exchange your knowledge and learn new skills through teaching others.</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 py-3 text-xs text-gray-500 mt-10">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div> 2025 SkillSwap. All rights reserved.</div>
                <div class="flex space-x-4">
                    <Link :href="route('about')" class="hover:underline">About</Link>
                    <a href="#" class="hover:underline">Terms</a>
                    <a href="#" class="hover:underline">Privacy</a>
                    <a href="#" class="hover:underline">Contact</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link } from '@inertiajs/vue3';
import Navbar from '../Components/Navbar.vue';

export default defineComponent({
    components: {
        Head,
        Link,
        Navbar,
    },

    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        featuredUsers: {
            type: Array,
            default: () => []
        },
        auth: {
            type: Object,
            default: () => ({})
        }
    },

    setup() {
        const imageLoadErrors = ref(new Set());
        return { imageLoadErrors };
    },

    data() {
        return {
            searchQuery: ''
        }
    },

    methods: {
        updateSearch() {
            // Clear any existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Show suggestions if there's text
            this.showSuggestions = this.searchQuery.length > 0;
            
            if (!this.showSuggestions) {
                this.searchSuggestions = [];
                return;
            }
            
            // Generate suggestions from featured users
            const searchLower = this.searchQuery.toLowerCase();
            const suggestions = new Set();
            
            // Add user names
            this.featuredUsers.forEach(user => {
                if (user.name.toLowerCase().includes(searchLower)) {
                    suggestions.add(user.name);
                }
            });
            
            // Add skill names
            this.featuredUsers.forEach(user => {
                user.teaching_skills?.forEach(skill => {
                    if (skill.name.toLowerCase().includes(searchLower)) {
                        suggestions.add(skill.name);
                    }
                });
                user.learning_skills?.forEach(skill => {
                    if (skill.name.toLowerCase().includes(searchLower)) {
                        suggestions.add(`Learn: ${skill.name}`);
                    }
                });
            });
            
            this.searchSuggestions = Array.from(suggestions).slice(0, 5); // Show top 5 suggestions
        },
        
        selectSuggestion(suggestion) {
            this.searchQuery = suggestion.replace('Learn: ', '');
            this.showSuggestions = false;
            this.searchSkills();
        },
        
        getInitials(name) {
            if (!name) return '';
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        },

        handleImageError(userId) {
            console.log('Image error for user:', userId);
            this.imageLoadErrors.add(userId);
        },

        requestSwap(userId) {
            // Get the user's teaching skills to show in the form
            const user = this.$page.props.auth.user;
            const recipient = this.featuredUsers.find(u => u.id === userId);
            
            if (!recipient) return;
            
            // Show a simple prompt to get the message
            const message = prompt('Enter your message for the swap request:');
            if (!message) return;
            
            // Get the first teaching skill of the recipient that the user wants to learn
            const skillWanted = recipient.teaching_skills[0]?.name || 'a skill';
            
            // Get the first teaching skill of the current user to offer
            const skillOffered = user.teaching_skills?.[0]?.name || 'a skill';
            
            // Send the request
            this.$inertia.post(route('requests.store'), {
                recipient_id: userId,
                skill_wanted: skillWanted,
                skill_offered: skillOffered,
                message: message,
                availability: 'Flexible',
                duration: 60
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    alert('Request sent successfully!');
                },
                onError: (errors) => {
                    console.error('Error sending request:', errors);
                    alert('Failed to send request. Please try again.');
                }
            });
        },

        searchSkills() {
            // Navigate to browse skills page with search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.trim().toLowerCase();
                
                // Map of keywords to categories
                const categoryKeywords = {
                    'Development': ['development', 'coding', 'programming', 'web'],
                    'Design': ['design', 'graphic', 'ui', 'ux'],
                    'Music': ['music', 'guitar', 'piano', 'singing'],
                    'Language': ['language', 'english', 'spanish', 'german'],
                    'Business': ['business', 'marketing', 'finance'],
                    'Lifestyle': ['fitness', 'yoga', 'cooking'],
                    'Photography': ['photography', 'photo', 'camera'],
                    'Education': ['education', 'teaching', 'tutoring']
                };

                // Detect category from search term
                let detectedCategory = null;
                for (const [category, keywords] of Object.entries(categoryKeywords)) {
                    if (keywords.some(keyword => query.includes(keyword))) {
                        detectedCategory = category;
                        break;
                    }
                }

                // Navigate with or without category
                this.$inertia.get(route('skills.browse'), {
                    search: query,
                    ...(detectedCategory && { category: detectedCategory })
                }, {
                    preserveState: true,
                    preserveScroll: true
                });
            }
        },
    }
});
</script>