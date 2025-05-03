<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Main Profile Content -->
      <div class="flex gap-8">
        <!-- Left Column - Profile Info -->
        <div class="w-80">
          <div class="text-center mb-6">
            <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center text-gray-400 text-2xl">
              {{ getInitials(user.name) }}
            </div>
            <h2 class="text-xl font-medium mb-1">{{ user.name }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ user.title || 'Web Developer & Designer' }}</p>
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
              {{ user.location || 'Location not set' }}
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
              {{ user.phone || 'Phone not set' }}
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

          <!-- Incoming Requests -->
          <div class="space-y-4">
            <div v-for="request in incomingRequests" :key="request.id" class="flex items-center justify-between p-4 bg-white border rounded-lg">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                  {{ getInitials(request.user.name) }}
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ request.user.name }}</div>
                  <div class="text-sm text-gray-500">
                    Wants to learn: {{ request.skill_wanted }}
                    <br />
                    Offers to teach: {{ request.skill_offered }}
                  </div>
                  <div class="text-xs text-gray-400 mt-1">
                    Received {{ formatDate(request.created_at) }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <template v-if="request.status === 'Pending'">
                  <button
                    @click="acceptRequest(request.id)"
                    class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200"
                  >
                    Accept
                  </button>
                  <button
                    @click="declineRequest(request.id)"
                    class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200"
                  >
                    Decline
                  </button>
                </template>
                <template v-else-if="request.status === 'Accepted'">
                  <Link
                    :href="route('messages.show', request.user.id)"
                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200"
                  >
                    Message
                  </Link>
                  <div class="text-sm text-green-600">Accepted</div>
                </template>
                <template v-else>
                  <div class="text-sm" :class="{
                    'text-red-600': request.status === 'Declined',
                    'text-gray-600': request.status === 'Cancelled'
                  }">
                    {{ request.status }}
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Outgoing Requests -->
          <h3 class="font-medium text-lg mt-8 mb-4">Outgoing Requests</h3>
          <div class="space-y-4">
            <div v-for="request in outgoingRequests" :key="request.id" class="group relative p-4 bg-white border rounded-lg">
              <button 
                v-if="request.status === 'Pending'"
                @click="cancelRequest(request.id)" 
                class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-gray-700 bg-gray-100 rounded-full shadow-sm z-50 hover:bg-gray-200"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                  {{ getInitials(request.recipient.name) }}
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ request.recipient.name }}</div>
                  <div class="text-sm text-gray-500">
                    You want to learn: {{ request.skill_wanted }}
                    <br />
                    You offer to teach: {{ request.skill_offered }}
                  </div>
                  <div class="text-xs text-gray-400 mt-1">
                    Sent {{ formatDate(request.created_at) }}
                  </div>
                </div>
              </div>
              <div class="mt-2 text-sm" :class="{
                'text-yellow-600': request.status === 'Pending',
                'text-green-600': request.status === 'Accepted',
                'text-red-600': request.status === 'Declined',
                'text-gray-600': request.status === 'Cancelled'
              }">
                {{ request.status }}
                <Link
                  v-if="request.status === 'Accepted'"
                  :href="route('messages.show', request.recipient.id)"
                  class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200"
                >
                  Message
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <Modal :show="showEditModal" @close="showEditModal = false" max-width="2xl">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-medium text-gray-900">
              Edit Profile
            </h2>
            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveProfile" class="space-y-6">
            <!-- Profile Photo -->
            <div class="flex items-center space-x-6">
              <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-xl">
                {{ getInitials(form.name) }}
              </div>
              <div>
                <button type="button" class="text-sm text-gray-600 hover:text-gray-900">
                  Change photo
                </button>
              </div>
            </div>

            <!-- Personal Info -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input 
                  v-model="form.phone" 
                  type="tel" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input 
                  v-model="form.location" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
              </div>
            </div>

            <!-- Skills I Can Teach -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Skills I Can Teach</label>
              <div class="flex flex-wrap gap-2 mb-3">
                <span 
                  v-for="skill in form.teachingSkills" 
                  :key="skill.name"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800"
                >
                  {{ skill.name }}
                  <button 
                    @click="removeTeachingSkill(skill)" 
                    type="button" 
                    class="text-gray-500 hover:text-gray-700"
                  >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </span>
              </div>
              <div class="flex gap-2">
                <input 
                  v-model="newTeachingSkill"
                  type="text"
                  placeholder="Add a skill you can teach..."
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                  @keyup.enter="addTeachingSkill"
                >
                <select
                  v-model="newTeachingCategory"
                  class="w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
                  <option value="">Category</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
                <button 
                  @click="addTeachingSkill"
                  type="button"
                  class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                >
                  Add
                </button>
              </div>
            </div>

            <!-- Skills I Want to Learn -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Skills I Want to Learn</label>
              <div class="flex flex-wrap gap-2 mb-3">
                <span 
                  v-for="skill in form.learningSkills" 
                  :key="skill.name"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800"
                >
                  {{ skill.name }}
                  <button 
                    @click="removeLearningSkill(skill)" 
                    type="button" 
                    class="text-gray-500 hover:text-gray-700"
                  >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </span>
              </div>
              <div class="flex gap-2">
                <input 
                  v-model="newLearningSkill"
                  type="text"
                  placeholder="Add a skill you want to learn..."
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                  @keyup.enter="addLearningSkill"
                >
                <select
                  v-model="newLearningCategory"
                  class="w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                >
                  <option value="">Category</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
                <button 
                  @click="addLearningSkill"
                  type="button"
                  class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                >
                  Add
                </button>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
              <button
                type="button"
                class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                @click="showEditModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
  user: Object,
  teachingSkills: Array,
  learningSkills: Array,
  incomingRequests: Array,
  outgoingRequests: Array,
});

const showEditModal = ref(false);

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  phone: props.user?.phone || '',
  location: props.user?.location || '',
  teachingSkills: props.teachingSkills || [],
  learningSkills: props.learningSkills || []
});

const newTeachingSkill = ref('');
const newTeachingCategory = ref('');
const newLearningSkill = ref('');
const newLearningCategory = ref('');

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

const incomingRequests = ref(props.incomingRequests || []);
const outgoingRequests = ref(props.outgoingRequests || []);

const getInitials = (name) => {
  if (!name) return '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const addTeachingSkill = () => {
  if (newTeachingSkill.value.trim() && newTeachingCategory.value) {
    form.teachingSkills.push({
      name: newTeachingSkill.value.trim(),
      category: newTeachingCategory.value
    });
    newTeachingSkill.value = '';
    newTeachingCategory.value = '';
  }
};

const removeTeachingSkill = (skill) => {
  form.teachingSkills = form.teachingSkills.filter(s => s.name !== skill.name);
};

const addLearningSkill = () => {
  if (newLearningSkill.value.trim() && newLearningCategory.value) {
    form.learningSkills.push({
      name: newLearningSkill.value.trim(),
      category: newLearningCategory.value
    });
    newLearningSkill.value = '';
    newLearningCategory.value = '';
  }
};

const removeLearningSkill = (skill) => {
  form.learningSkills = form.learningSkills.filter(s => s.name !== skill.name);
};

const acceptRequest = (requestId) => {
  router.post(route('requests.accept', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      incomingRequests.value = incomingRequests.value.filter(r => r.id !== requestId);
    }
  });
};

const declineRequest = (requestId) => {
  router.post(route('requests.decline', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      incomingRequests.value = incomingRequests.value.filter(r => r.id !== requestId);
    }
  });
};

const cancelRequest = (requestId) => {
  router.post(route('requests.cancel', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      outgoingRequests.value = outgoingRequests.value.filter(r => r.id !== requestId);
    }
  });
};

const saveProfile = () => {
  form.patch(route('profile.update'), {
    onSuccess: () => {
      showEditModal.value = false;
    }
  });
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

watch(() => props.incomingRequests, (newRequests) => {
  incomingRequests.value = newRequests;
}, { deep: true });

watch(() => props.outgoingRequests, (newRequests) => {
  outgoingRequests.value = newRequests;
}, { deep: true });
</script>