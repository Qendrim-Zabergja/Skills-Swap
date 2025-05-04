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
                    <div class="max-w-xl mx-auto flex items-center gap-2">
                        <div class="flex-1 relative">
                            <input 
                                type="text" 
                                v-model="searchQuery" 
                                placeholder="Search skills (e.g., Graphic Design)" 
                                class="w-full px-4 py-2 rounded border border-gray-200 focus:outline-none focus:border-gray-300 bg-white"
                            />
                        </div>
                        <button 
                            @click="searchSkills" 
                            class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800 whitespace-nowrap"
                        >
                            Find Skills
                        </button>
                    </div>
                </div>
            </section>

            <!-- Featured Skills Section -->
            <section class="py-8 px-4 max-w-6xl mx-auto">
                <h2 class="text-xl font-semibold mb-8">Featured Skill Offers</h2>
                
                <div v-if="featuredSkills.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div v-for="skill in featuredSkills" :key="skill.id" class="bg-white rounded-lg p-6 border border-gray-100">
                        <!-- User Info -->
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                <img v-if="skill.user.profile_image" :src="'/storage/' + skill.user.profile_image"
                                    class="w-full h-full object-cover" :alt="skill.user.first_name">
                                <span v-else class="text-gray-500 text-sm">{{ getInitials(skill.user.first_name,
                                    skill.user.last_name) }}</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ skill.user.first_name }} {{ skill.user.last_name }}</p>
                                <p class="text-gray-400 text-xs">{{ skill.user.location }}</p>
                            </div>
                        </div>

                        <!-- Skill Info -->
                        <h3 class="font-semibold text-lg mb-2">{{ skill.title }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ truncate(skill.description, 120) }}</p>

                        <!-- Tags and Action -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span v-for="tag in skill.looking_for" :key="tag" class="text-xs bg-gray-50 text-gray-600 px-2 py-1 rounded">
                                Looking for: {{ tag }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-1">
                                <span class="text-yellow-400">★</span>
                                <span class="text-sm">{{ skill.rating }} • {{ skill.reviews }} swaps</span>
                            </div>
                            <Link :href="route('skills.show', skill.id)"
                                class="bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-800">Request Swap</Link>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-10 bg-gray-50 rounded-lg mb-8">
                    <p class="text-gray-500">No skills available yet. Be the first to share your expertise!</p>
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
                    <a href="#" class="hover:underline">About</a>
                    <a href="#" class="hover:underline">Terms</a>
                    <a href="#" class="hover:underline">Privacy</a>
                    <a href="#" class="hover:underline">Contact</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
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
    },

    data() {
        return {
            searchQuery: '',
            featuredSkills: [
                {
                    id: 1,
                    title: 'Graphic Design & Brand Identity',
                    description: 'I can teach you how to create stunning logos, brand identities, and marketing materials using Adobe Creative Suite.',
                    user: {
                        first_name: 'Alex',
                        last_name: 'Morgan',
                        profile_image: null,
                        location: 'New York, USA'
                    },
                    looking_for: ['Web Development', 'Digital Marketing'],
                    rating: 4.8,
                    reviews: 24
                },
                {
                    id: 2,
                    title: 'Full-Stack Web Development',
                    description: 'Learn how to build responsive websites and web applications using React, Node.js, and modern web technologies.',
                    user: {
                        first_name: 'Sarah',
                        last_name: 'Chen',
                        profile_image: null,
                        location: 'San Francisco, USA'
                    },
                    looking_for: ['Photography', 'UX/UI Design'],
                    rating: 4.9,
                    reviews: 18
                },
                {
                    id: 3,
                    title: 'Guitar Lessons for Beginners',
                    description: 'I\'ll teach you to play guitar from scratch - chords, strumming patterns, and your favorite songs in just a few sessions.',
                    user: {
                        first_name: 'Miguel',
                        last_name: 'Rodriguez',
                        profile_image: null,
                        location: 'Los Angeles, USA'
                    },
                    looking_for: ['Language (English)', 'Cooking'],
                    rating: 4.7,
                    reviews: 31
                }
            ]
        }
    },

    mounted() {
        // Comment out the fetchFeaturedSkills call for now since we're using static data
        // this.fetchFeaturedSkills();
    },

    methods: {
        getInitials(firstName, lastName) {
            firstName = firstName || '';
            lastName = lastName || '';
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        },

        truncate(text, length) {
            if (!text) return '';
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        },

        searchSkills() {
            // Navigate to browse skills page with search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.trim();
                
                this.$inertia.get(route('skills.browse'), {
                    search: query,
                    categories: [] // Also set the categories array for the filter
                }, {
                    preserveState: true,
                    preserveScroll: true
                });
            }
        },

        detectCategory(query) {
            // List of predefined categories to match against
            const categoryKeywords = {
                'Development': ['php', 'javascript', 'python', 'web', 'react', 'vue', 'laravel', 'node', 'coding', 'programming'],
                'Design': ['design', 'graphic', 'ui', 'ux', 'logo', 'branding', 'illustration', 'photoshop', 'figma'],
                'Music': ['music', 'guitar', 'piano', 'singing', 'drums', 'bass', 'violin'],
                'Language': ['language', 'english', 'spanish', 'german', 'french', 'italian', 'chinese', 'japanese'],
                'Business': ['business', 'marketing', 'seo', 'finance', 'accounting', 'management'],
                'Lifestyle': ['fitness', 'yoga', 'meditation', 'cooking', 'baking', 'nutrition'],
                'Photography': ['photography', 'photo', 'camera', 'lightroom', 'portraits'],
                'Education': ['math', 'science', 'history', 'teaching', 'tutoring']
            };

            // Convert query to lowercase for case-insensitive matching
            query = query.toLowerCase();
            
            // Check if query contains any category keywords
            for (const [category, keywords] of Object.entries(categoryKeywords)) {
                if (keywords.some(keyword => query.includes(keyword))) {
                    return category;
                }
            }
            
            return null; // Return null if no category matches
        },
    }
});
</script>
