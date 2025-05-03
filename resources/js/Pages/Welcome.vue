<template>

    <Head title="Welcome" />

    <div class="min-h-screen bg-white">
        <!-- Header -->
        <Navbar />

        <main>
            <!-- Hero Section -->
            <section class="py-12 px-4 max-w-6xl mx-auto">
                <div class="text-center mb-10">
                    <h1 class="text-6xl font-bold mb-2">Swap Skills, Learn & Teach For Free!</h1>
                    <p class="text-gray-600 mb-6 text-2xl">Exchange your expertise with others. No money involved - just
                        <br> knowledge sharing.</p>
                </div>
            </section>

            <!-- Search Input and Find Skills Button -->
            <div class="flex justify-center items-center gap-4 mb-6">
                <input type="text" v-model="searchQuery" placeholder="Search skills..."
                    class="border border-gray-300 rounded px-4 py-2 w-80" />
                <button @click="searchSkills" class="bg-black text-white px-4 py-2 rounded">
                    Find Skills
                </button>
            </div>

            <!-- Featured Skills Section -->
            <section class="py-8 px-4 max-w-6xl mx-auto">
                <h2 class="text-xl font-bold mb-6">Featured Skill Offers</h2>
                <div v-if="featuredSkills.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="skill in featuredSkills" :key="skill.id" class="border rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                <img v-if="skill.user.profile_image" :src="'/storage/' + skill.user.profile_image"
                                    class="w-10 h-10 rounded-full object-cover" :alt="skill.user.first_name">
                                <span v-else class="text-gray-500">{{ getInitials(skill.user.first_name,
                                    skill.user.last_name) }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ skill.title }}</h3>
                                <p class="text-sm text-gray-500">{{ skill.user.first_name }} {{ skill.user.last_name }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">{{ truncate(skill.description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ skill.category }}</span>
                            <Link :href="route('skills.show', skill.id)"
                                class="text-xs bg-black text-white px-3 py-1 rounded">Contact to Learn</Link>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-10 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">No skills available yet. Be the first to share your expertise!</p>
                    <Link v-if="$page.props.auth.user" :href="route('profile.edit')"
                        class="bg-black text-white px-4 py-2 rounded mt-4 inline-block">Add Your Skills</Link>
                    <Link v-else :href="route('register')"
                        class="bg-black text-white px-4 py-2 rounded mt-4 inline-block">Sign Up to Share</Link>
                </div>
            </section>

            <!-- How It Works Section -->
            <section class="py-12 px-4 max-w-6xl mx-auto">
                <h2 class="text-xl font-bold mb-8 text-center">How SkillSwap Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-4">
                            <span class="font-bold">1</span>
                        </div>
                        <h3 class="font-semibold mb-2">List Your Skills</h3>
                        <p class="text-sm text-gray-600">Create a profile and list the skills you can teach others.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-4">
                            <span class="font-bold">2</span>
                        </div>
                        <h3 class="font-semibold mb-2">Find & Connect</h3>
                        <p class="text-sm text-gray-600">Browse skills you want to learn and connect with potential
                            teachers.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-4">
                            <span class="font-bold">3</span>
                        </div>
                        <h3 class="font-semibold mb-2">Teach & Learn</h3>
                        <p class="text-sm text-gray-600">Exchange your knowledge and learn new skills through teaching
                            others.</p>
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
            featuredSkills: []
        }
    },

    mounted() {
        this.fetchFeaturedSkills();
    },

    methods: {
        async fetchFeaturedSkills() {
            try {
                const response = await axios.get('/api/featured-skills');
                this.featuredSkills = response.data;
            } catch (error) {
                console.error('Failed to fetch featured skills', error);
            }
        },

        getInitials(firstName, lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`;
        },

        truncate(text, length) {
            if (!text) return '';
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        }
    }
});
</script>
