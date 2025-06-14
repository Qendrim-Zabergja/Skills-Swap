<template>
  <Head title="Browse Skills" />
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Link href="/" class="text-xl font-bold">SkillSwap</Link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link 
                href="/browse" 
                class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                Browse Skills
              </Link>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <div class="flex items-center space-x-4">
              <Link 
                href="/profile" 
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                My Profile
              </Link>
              <Link
                href="/logout"
                method="post"
                as="button"
                class="bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm font-medium"
              >
                Log out
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-2xl font-bold mb-2">Browse Skills</h1>
      <p class="text-gray-600 mb-8">Find skills to learn or people to teach</p>

      <div class="flex gap-8">
        <!-- Filters Sidebar -->
        <div class="w-64 flex-shrink-0">
          <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="font-semibold mb-4">Filters</h2>
            
            <!-- Skill Categories -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Skill Categories</h3>
              <div class="space-y-2">
                <label v-for="category in categories" :key="category" class="flex items-center">
                  <input 
                    type="checkbox" 
                    :value="category"
                    v-model="selectedCategories"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-600">{{ category }}</span>
                </label>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Rating</h3>
              <div class="space-y-2">
                <label v-for="rating in ratings" :key="rating.value" class="flex items-center">
                  <input 
                    type="radio" 
                    :value="rating.value"
                    v-model="selectedRating"
                    name="rating"
                    class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-600 flex items-center">
                    <span class="flex">
                      <span v-for="i in rating.value" :key="i" class="text-yellow-400">★</span>
                      <span v-for="i in (5-rating.value)" :key="i+5" class="text-gray-300">★</span>
                    </span>
                    <span class="ml-1">& up</span>
                  </span>
                </label>
              </div>
            </div>

            <!-- Exchange Preferences -->
            <div class="mb-6">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Exchange Preferences</h3>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="lookingForMySkills" 
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-600">Looking for my skills</span>
                </label>
                <label class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="offeringSkillsIWant"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-600">Offering skills I want</span>
                </label>
                <label class="flex items-center">
                  <input 
                    type="checkbox"
                    v-model="perfectMatchesOnly"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-600">Perfect matches only</span>
                </label>
              </div>
            </div>

            <div class="flex gap-2">
              <button 
                @click="resetFilters"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Reset
              </button>
              <button 
                @click="applyFilters"
                class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800"
              >
                Apply Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
          <!-- Search Bar -->
          <div class="mb-6">
            <div class="relative">
              <input 
                v-model="search" 
                type="text" 
                placeholder="Search skills or keywords..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Users Grid -->
          <div class="grid grid-cols-1 gap-6">

            <div v-for="user in filteredUsers" :key="user.id" class="bg-white p-6 rounded-lg shadow">
              <div class="flex items-start justify-between">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                      <img 
                        v-if="user.profile_photo_url" 
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
                    <span v-for="i in 5" :key="i" class="text-lg">★</span>
                  </div>
                  <span class="ml-1 text-gray-600">5</span>
                </div>
              </div>

              <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Looking to learn:</h4>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span 
                    v-for="skill in user.learning_skills" 
                    :key="skill.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                  >
                    {{ skill.name }}
                  </span>
                  <span v-if="user.learning_skills.length === 0" class="text-sm text-gray-500">
                    No learning skills listed
                  </span>
                </div>
              </div>

              <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Can teach:</h4>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span 
                    v-for="skill in user.teaching_skills" 
                    :key="skill.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                  >
                    {{ skill.name }}
                  </span>
                  <span v-if="user.teaching_skills.length === 0" class="text-sm text-gray-500">
                    No teaching skills listed
                  </span>
                </div>
              </div>

              <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                  {{ user.swaps_completed || 0 }} swaps completed
                </div>
                <button 
                  v-if="auth.user && auth.user.id !== user.id"
                  @click="requestSwap(user.id)"
                  class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800"
                >
                  Request Swap
                </button>
                <div v-else class="w-24"></div> <!-- Empty div to maintain layout -->
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex justify-center items-center gap-2">
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1"
              class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50"
            >
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <span class="px-4 py-2 text-sm font-medium text-gray-700">{{ currentPage }}</span>
            <button 
              @click="nextPage"
              class="p-2 rounded-md hover:bg-gray-100"
            >
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  skills: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  initialSearch: {
    type: String,
    default: ''
  },
  initialCategory: {
    type: String,
    default: ''
  },
  pagination: {
    type: Object,
    default: () => ({})
  },
  auth: {
    type: Object,
    required: true
  }
});

// State
const searchQuery = ref('');
const selectedCategories = ref([]);
const selectedSkills = ref([]);
const showFilters = ref(false);
const imageLoadErrors = ref(new Set());
const offeringSkillsIWant = ref(false);
const perfectMatchesOnly = ref(false);
const currentPage = ref(1);
const searchTimeout = ref(null);

// Categories
const categories = [
  'Design',
  'Development',
  'Music',
  'Language',
  'Business',
  'Marketing',
  'Photography',
  'Writing'
];

// Ratings
const ratings = [
  { value: 4, label: '4 stars' },
  { value: 3, label: '3 stars' },
  { value: 2, label: '2 stars' }
];

// Methods
const getInitials = (name) => {
  if (!name) return '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const resetFilters = () => {
  selectedCategories.value = [];
  selectedRating.value = null;
  lookingForMySkills.value = false;
  offeringSkillsIWant.value = false;
  perfectMatchesOnly.value = false;
  search.value = '';
  applyFilters();
};

const applyFilters = () => {
  console.log('Applying filters with search:', search.value);
  window.Inertia.get('/browse', {
    search: search.value,
    categories: selectedCategories.value,
    rating: selectedRating.value,
    lookingForMySkills: lookingForMySkills.value,
    offeringSkillsIWant: offeringSkillsIWant.value,
    perfectMatchesOnly: perfectMatchesOnly.value,
    page: currentPage.value
  }, {
    preserveState: true,
    preserveScroll: true
  });
};

const requestSwap = (userId) => {
  // Get the user's teaching skills to show in the form
  const user = props.auth.user;
  const recipient = props.skills.find(skill => skill.id === userId);
  
  if (!recipient) return;
  
  // Show a simple prompt to get the message
  const message = prompt('Enter your message for the swap request:');
  if (!message) return;
  
  // Get the first teaching skill of the recipient that the user wants to learn
  const skillWanted = recipient.teaching_skills[0]?.name || 'a skill';
  
  // Get the first teaching skill of the current user to offer
  const skillOffered = user.teaching_skills?.[0]?.name || 'a skill';
  
  // Send the request
  window.Inertia.post(route('requests.store'), {
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
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    applyFilters();
  }
};

const nextPage = () => {
  currentPage.value++;
  applyFilters();
};

const handleImageError = (userId) => {
  console.log('Image error for user:', userId);
  imageLoadErrors.value.add(userId);
};

// Computed
const filteredUsers = computed(() => {
  console.log('FILTERED USERS RUNNING');
  console.log('Props skills:', props.skills);
  if (props.skills.length > 0) {
    console.log('First user photo URL:', props.skills[0].profile_photo_url);
  }
  
  // Filter out the current user
  const currentUserId = props.auth.user.id;
  let filtered = props.skills.filter(user => user.id !== currentUserId);
  console.log('First user:', filtered[0]);

  // Apply search filter
  if (search.value) {
    const searchLower = search.value.toLowerCase();
    filtered = filtered.filter(user => {
      const hasMatchingTeaching = user.teaching_skills.some(skill => 
        skill.name.toLowerCase().includes(searchLower) ||
        (skill.description && skill.description.toLowerCase().includes(searchLower)) ||
        (skill.category && skill.category.toLowerCase().includes(searchLower))
      );
      const hasMatchingLearning = user.learning_skills.some(skill => 
        skill.name.toLowerCase().includes(searchLower) ||
        (skill.description && skill.description.toLowerCase().includes(searchLower)) ||
        (skill.category && skill.category.toLowerCase().includes(searchLower))
      );
      return hasMatchingTeaching || hasMatchingLearning;
    });
  }

  // Apply category filter
  if (selectedCategories.value.length > 0) {
    filtered = filtered.filter(user => {
      const allSkills = [...user.teaching_skills, ...user.learning_skills];
      return allSkills.some(skill => 
        selectedCategories.value.includes(skill.category)
      );
    });
  }

  // Apply rating filter
  if (selectedRating.value) {
    filtered = filtered.filter(user => 
      (user.rating || 0) >= selectedRating.value
    );
  }

  return filtered;
});

// Watch search changes
watch(search, (newValue) => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  searchTimeout.value = setTimeout(() => {
    applyFilters();
  }, 300);
});

// Initialize on mount
onMounted(() => {
  console.log('Users:', props.skills);
  
  const urlParams = new URLSearchParams(window.location.search);
  search.value = urlParams.get('search') || props.initialSearch || '';
  selectedCategories.value = urlParams.get('category') ? [urlParams.get('category')] : 
                          urlParams.get('categories') ? urlParams.get('categories').split(',') : 
                          props.initialCategory ? [props.initialCategory] : [];
});
</script>