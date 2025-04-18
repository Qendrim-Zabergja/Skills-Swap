<template>
    <Head title="Welcome" />
  
    <div class="min-h-screen bg-white">
      <!-- Header -->
      <header class="border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 flex items-center">
          <Link :href="route('home')" class="font-bold text-lg">SkillSwap</Link>
          
          <!-- Navigation slightly right of center -->
          <div class="flex-grow basis-[5%]"></div>
          
          <nav class="hidden md:flex">
            <Link href="#" class="mr-6 text-sm">Browse Skills</Link>
            <Link :href="route('profile.edit')" class="text-sm">My Profile</Link>
          </nav>
          
          <div class="flex-grow"></div>
          
          <div>
            <div v-if="$page.props.auth.user">
              <Link :href="route('logout')" method="post" as="button" class="bg-black text-white px-3 py-1 text-sm rounded">
                Log out
              </Link>
            </div>
            <div v-else>
              <Link :href="route('login')" class="mr-4 text-sm">Log in</Link>
              <Link :href="route('register')" class="bg-black text-white px-3 py-1 text-sm rounded">Sign up</Link>
            </div>
          </div>
        </div>
      </header>
  
      <main>
        <!-- Hero Section with Search -->
        <section class="py-16 px-4 bg-gray-100">
          <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">Swap Skills, Learn & Teach for Free!</h1>
            <p class="text-gray-600 mb-10 max-w-3xl mx-auto">Exchange your expertise with others. No money involved - just knowledge sharing.</p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-8">
              <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-grow">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                  <input type="text" placeholder="Search skills (e.g., Graphic Design)" class="pl-10 w-full py-2 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black">
                </div>
                <button class="bg-black text-white px-6 py-2 rounded-md">Find Skills</button>
              </div>
            </div>
            
            <Link :href="route('register')" class="bg-black text-white px-6 py-3 rounded-md font-medium">Get Started</Link>
          </div>
        </section>
  
        <!-- Featured Skills Section -->
        <section class="py-16 px-4 max-w-6xl mx-auto">
          <h2 class="text-2xl font-bold mb-8 text-center">Featured Skill Offers</h2>
          
          <div v-if="featuredSkills.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="skill in featuredSkills" :key="skill.id" class="border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3 overflow-hidden">
                  <img v-if="skill.user.profile_image" :src="'/storage/' + skill.user.profile_image" class="w-12 h-12 rounded-full object-cover" :alt="skill.user.first_name">
                  <span v-else class="text-gray-500 font-medium">{{ getInitials(skill.user.first_name, skill.user.last_name) }}</span>
                </div>
                <div>
                  <h3 class="font-semibold text-lg">{{ skill.user.first_name }} {{ skill.user.last_name }}</h3>
                </div>
              </div>
              <h4 class="font-bold mb-2">{{ skill.title }}</h4>
              <p class="text-sm text-gray-600 mb-4">{{ truncate(skill.description, 100) }}</p>
              <div class="flex justify-between items-center">
                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ skill.category }}</span>
                <Link :href="route('skills.show', skill.id)" class="text-xs bg-black text-white px-3 py-1 rounded">Contact to Learn</Link>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-16 bg-gray-50 rounded-lg">
            <p class="text-gray-500 mb-4">No skills available yet. Be the first to share your expertise!</p>
            <Link v-if="$page.props.auth.user" :href="route('profile.edit')" class="bg-black text-white px-4 py-2 rounded inline-block">Add Your Skills</Link>
            <Link v-else :href="route('register')" class="bg-black text-white px-4 py-2 rounded inline-block">Sign Up to Share</Link>
          </div>
        </section>
  
        <!-- How It Works Section -->
        <section class="py-16 px-4 max-w-6xl mx-auto">
          <h2 class="text-2xl font-bold mb-12 text-center">How SkillSwap Works</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="text-center">
              <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-6">
                <span class="font-bold text-xl">1</span>
              </div>
              <h3 class="font-semibold text-lg mb-3">List Your Skills</h3>
              <p class="text-gray-600">Create a profile and list the skills you can teach others.</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-6">
                <span class="font-bold text-xl">2</span>
              </div>
              <h3 class="font-semibold text-lg mb-3">Find & Connect</h3>
              <p class="text-gray-600">Browse skills you want to learn and connect with potential teachers.</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-6">
                <span class="font-bold text-xl">3</span>
              </div>
              <h3 class="font-semibold text-lg mb-3">Teach & Learn</h3>
              <p class="text-gray-600">Exchange your knowledge and learn new skills through teaching others.</p>
            </div>
          </div>
        </section>
      </main>
  
      <!-- Footer -->
      <footer class="bg-gray-100 py-6 text-sm text-gray-500">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">© 2023 SkillSwap. All rights reserved.</div>
          <div class="flex space-x-6">
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
  import { Head, Link } from '@inertiajs/inertia-vue3';
  import axios from 'axios';
  
  export default defineComponent({
    components: {
      Head,
      Link,
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