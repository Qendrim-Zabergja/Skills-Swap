<template>
  <div class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex-shrink-0 flex items-center">
            <Link href="/" class="text-xl font-bold">
              SkillSwap
            </Link>
          </div>
          <div class="flex items-center space-x-6">
            <Link href="/browse-skills" class="text-gray-600 hover:text-gray-900">
              Browse Skills
            </Link>
            <Link href="/my-profile" class="text-gray-900">
              My Profile
            </Link>
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600">
              0
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Tabs -->
      <div class="border-b border-gray-200 mb-8">
        <div class="flex space-x-8">
          <button 
            class="border-b-2 border-gray-900 py-4 px-1 text-sm font-medium text-gray-900"
          >
            Swap Requests
          </button>
          <button 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
          >
            Messages
          </button>
        </div>
      </div>

      <div class="flex gap-8">
        <!-- Left Column - Profile Info -->
        <div class="w-80">
          <div class="text-center mb-6">
            <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center text-gray-400 text-2xl">
              {{ getInitials(user.name) }}
            </div>
            <h2 class="text-xl font-medium mb-1">{{ user.name }}</h2>
            <p class="text-sm text-gray-500 mb-4">Web Developer & Designer</p>
            <button 
              @click="showEditModal = true"
              class="w-full bg-gray-900 text-white py-2 px-4 rounded text-sm font-medium hover:bg-gray-800"
            >
              Edit Profile
            </button>
          </div>

          <div class="space-y-4">
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ user.location || 'San Francisco, CA' }}
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              {{ user.email }}
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              {{ user.phone || '+1 (555) 123-4567' }}
            </div>
          </div>

          <!-- Skills -->
          <div class="mt-8">
            <h3 class="font-medium mb-3">Skills I Can Teach</h3>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="skill in teachingSkills" 
                :key="skill.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
              >
                {{ skill.name }}
              </span>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="font-medium mb-3">Skills I Want to Learn</h3>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="skill in learningSkills" 
                :key="skill.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
              >
                {{ skill.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right Column - Requests -->
        <div class="flex-1">
          <!-- Incoming Requests -->
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-white border rounded-lg">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                  EJ
                </div>
                <div>
                  <h4 class="font-medium">Emily Johnson</h4>
                  <div class="text-sm text-gray-500">
                    <p>Wants to learn: Web Development</p>
                    <p>Offers to teach: Digital Marketing</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm border rounded-md hover:bg-gray-50">
                  Message
                </button>
                <button class="px-3 py-1 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800">
                  Accept
                </button>
                <button class="px-3 py-1 text-sm text-red-600 hover:text-red-700">
                  Decline
                </button>
              </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-white border rounded-lg">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                  MC
                </div>
                <div>
                  <h4 class="font-medium">Michael Chan</h4>
                  <div class="text-sm text-gray-500">
                    <p>Wants to learn: React.js</p>
                    <p>Offers to teach: Photography</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm border rounded-md hover:bg-gray-50">
                  Message
                </button>
                <button class="px-3 py-1 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800">
                  Accept
                </button>
                <button class="px-3 py-1 text-sm text-red-600 hover:text-red-700">
                  Decline
                </button>
              </div>
            </div>
          </div>

          <!-- Outgoing Requests -->
          <h3 class="font-medium text-lg mt-8 mb-4">Outgoing Requests</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-white border rounded-lg">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                  SW
                </div>
                <div>
                  <h4 class="font-medium">Sarah Williams</h4>
                  <div class="text-sm text-gray-500">
                    <p>You want to learn: Spanish Language</p>
                    <p>You offer to teach: UX/UI Design</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm border rounded-md hover:bg-gray-50">
                  Message
                </button>
                <button class="px-3 py-1 text-sm text-red-600 hover:text-red-700">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <EditProfileModal 
    :show="showEditModal"
    :user="user"
    :teaching-skills="teachingSkills"
    :learning-skills="learningSkills"
    @close="showEditModal = false"
    @saved="handleProfileSaved"
  />
</template>

<script>
import { Link } from '@inertiajs/vue3';
import EditProfileModal from '@/Components/EditProfileModal.vue';

export default {
  components: {
    Link,
    EditProfileModal,
  },

  props: {
    user: Object,
    teachingSkills: Array,
    learningSkills: Array,
  },

  data() {
    return {
      showEditModal: false,
    };
  },

  methods: {
    getInitials(name) {
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
    },

    handleProfileSaved() {
      this.showEditModal = false;
      // Refresh the page or update the data as needed
    },
  },
};
</script>