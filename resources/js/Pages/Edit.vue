<template>
  <Head title="Profile" />

  <div class="max-w-4xl mx-auto my-10 p-4">
    <div class="flex flex-col md:flex-row gap-8">
      <!-- Left sidebar with user info -->
      <div class="w-full md:w-1/3">
        <div class="bg-white p-6 border rounded-lg">
          <div class="flex justify-center mb-4">
            <div v-if="user.profile_image" class="w-24 h-24 rounded-full overflow-hidden">
              <img :src="'/storage/' + user.profile_image" class="w-full h-full object-cover" :alt="user.first_name">
            </div>
            <div v-else class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-2xl">
              {{ getInitials(user.first_name, user.last_name) }}
            </div>
          </div>
          
          <div class="text-center mb-6">
            <h1 class="text-xl font-bold">{{ user.first_name }} {{ user.last_name }}</h1>
            <button @click="editProfile = true" class="text-sm text-gray-600 underline mt-2">Edit Profile</button>
          </div>
          
          <div class="space-y-3">
            <div v-if="user.location" class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ user.location }}</span>
            </div>
            
            <div v-if="user.email" class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>{{ user.email }}</span>
            </div>
            
            <div v-if="user.phone" class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span>{{ user.phone }}</span>
            </div>
          </div>
          
          <div class="mt-6">
            <h2 class="font-semibold mb-2">Skills I Want to Learn</h2>
            <div class="flex flex-wrap gap-2">
              <span v-for="(tag, index) in skillTags" :key="index" class="bg-gray-100 text-xs px-2 py-1 rounded">
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right content area -->
      <div class="w-full md:w-2/3">
        <!-- Tabs -->
        <div class="border-b mb-6">
          <div class="flex space-x-8">
            <button 
              @click="activeTab = 'skills'" 
              class="py-2 px-1 border-b-2" 
              :class="activeTab === 'skills' ? 'border-black font-semibold' : 'border-transparent text-gray-500'"
            >
              My Skills
            </button>
            <button 
              @click="activeTab = 'messages'" 
              class="py-2 px-1 border-b-2" 
              :class="activeTab === 'messages' ? 'border-black font-semibold' : 'border-transparent text-gray-500'"
            >
              Messages
            </button>
          </div>
        </div>
        
        <!-- Skills Tab -->
        <div v-if="activeTab === 'skills'">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">My Skills</h2>
            <button @click="showAddSkillForm = true" class="bg-black text-white px-3 py-1 text-sm rounded">Add New Skill</button>
          </div>
          
          <div v-if="user.skills && user.skills.length > 0" class="space-y-4">
            <div v-for="skill in user.skills" :key="skill.id" class="border rounded-lg p-4">
              <h3 class="font-semibold text-lg mb-1">{{ skill.title }}</h3>
              <span class="inline-block bg-gray-100 text-xs px-2 py-1 rounded mb-3">{{ skill.category }}</span>
              <p class="text-sm text-gray-600 mb-4">{{ skill.description }}</p>
              <div class="flex justify-end space-x-2">
                <button @click="editSkill(skill)" class="text-xs bg-gray-100 px-3 py-1 rounded">Edit</button>
                <button @click="deleteSkill(skill.id)" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded">Delete</button>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-10 bg-gray-50 rounded-lg">
            <p class="text-gray-500 mb-4">You haven't added any skills yet.</p>
            <button @click="showAddSkillForm = true" class="bg-black text-white px-4 py-2 rounded">Add Your First Skill</button>
          </div>
        </div>
        
        <!-- Messages Tab -->
        <div v-if="activeTab === 'messages'">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Messages</h2>
          </div>
          
          <!-- Incoming Requests -->
          <div class="mb-8">
            <h3 class="font-semibold mb-4">Incoming Requests</h3>
            <div v-if="incomingRequests.length > 0" class="space-y-4">
              <div v-for="request in incomingRequests" :key="request.id" class="border rounded-lg p-4">
                <div class="flex items-center mb-3">
                  <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3 text-xs">
                    {{ getInitials(request.sender.first_name, request.sender.last_name) }}
                  </div>
                  <div>
                    <div class="font-semibold">{{ request.sender.first_name }} {{ request.sender.last_name }}</div>
                    <div class="text-xs text-gray-500">Wants to learn: {{ request.skill.title }}</div>
                  </div>
                  <div class="ml-auto text-xs">
                    <span :class="getStatusClass(request.status)">{{ request.status }}</span>
                  </div>
                </div>
                <p class="text-sm mb-3">{{ request.message }}</p>
                <div v-if="request.status === 'pending'" class="flex justify-end space-x-2">
                  <button @click="updateRequestStatus(request.id, 'accepted')" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded">Accept</button>
                  <button @click="updateRequestStatus(request.id, 'rejected')" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded">Decline</button>
                </div>
                <Link v-if="request.status === 'accepted'" :href="route('messages.index', request.id)" class="text-xs bg-black text-white px-3 py-1 rounded">Message</Link>
              </div>
            </div>
            <div v-else class="text-center py-6 bg-gray-50 rounded-lg">
              <p class="text-gray-500">No incoming requests yet.</p>
            </div>
          </div>
          
          <!-- Outgoing Requests -->
          <div>
            <h3 class="font-semibold mb-4">Outgoing Requests</h3>
            <div v-if="outgoingRequests.length > 0" class="space-y-4">
              <div v-for="request in outgoingRequests" :key="request.id" class="border rounded-lg p-4">
                <div class="flex items-center mb-3">
                  <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3 text-xs">
                    {{ getInitials(request.receiver.first_name, request.receiver.last_name) }}
                  </div>
                  <div>
                    <div class="font-semibold">{{ request.receiver.first_name }} {{ request.receiver.last_name }}</div>
                    <div class="text-xs text-gray-500">Skill: {{ request.skill.title }}</div>
                  </div>
                  <div class="ml-auto text-xs">
                    <span :class="getStatusClass(request.status)">{{ request.status }}</span>
                  </div>
                </div>
                <p class="text-sm mb-3">{{ request.message }}</p>
                <Link v-if="request.status === 'accepted'" :href="route('messages.index', request.id)" class="text-xs bg-black text-white px-3 py-1 rounded">Message</Link>
              </div>
            </div>
            <div v-else class="text-center py-6 bg-gray-50 rounded-lg">
              <p class="text-gray-500">No outgoing requests yet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div v-if="editProfile" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Profile</h2>
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
              <input 
                type="text" 
                v-model="profileForm.first_name" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
              <input 
                type="text" 
                v-model="profileForm.last_name" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
              >
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              type="email" 
              v-model="profileForm.email" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input 
              type="text" 
              v-model="profileForm.location" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input 
              type="text" 
              v-model="profileForm.phone" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea 
              v-model="profileForm.bio" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
            <input 
              type="file" 
              @change="handleImageUpload" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            >
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <button 
              type="button" 
              @click="editProfile = false" 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-black text-white rounded-md"
              :disabled="form.processing"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Add Skill Modal -->
    <div v-if="showAddSkillForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ editingSkill ? 'Edit Skill' : 'Add New Skill' }}</h2>
        <form @submit.prevent="saveSkill" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Skill Title</label>
            <input 
              type="text" 
              v-model="skillForm.title" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select 
              v-model="skillForm.category" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
              required
            >
              <option value="">Select a category</option>
              <option value="Programming">Programming</option>
              <option value="Design">Design</option>
              <option value="Marketing">Marketing</option>
              <option value="Language">Language</option>
              <option value="Music">Music</option>
              <option value="Cooking">Cooking</option>
              <option value="Fitness">Fitness</option>
              <option value="Other">Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea 
              v-model="skillForm.description" 
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
              required
            ></textarea>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <button 
              type="button" 
              @click="cancelSkillForm" 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-black text-white rounded-md"
              :disabled="skillForm.processing"
            >
              {{ editingSkill ? 'Update Skill' : 'Add Skill' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

export default defineComponent({
  components: {
    Head,
    Link,
  },

  props: {
    user: Object,
    incomingRequests: Array,
    outgoingRequests: Array,
    mustVerifyEmail: Boolean,
    status: String,
  },

  data() {
    return {
      activeTab: 'skills',
      editProfile: false,
      showAddSkillForm: false,
      editingSkill: null,
      skillTags: ['Programming', 'Design', 'Marketing', 'Spanish'],
      profileForm: {
        first_name: '',
        last_name: '',
        email: '',
        location: '',
        phone: '',
        bio: '',
        profile_image: null
      },
      skillForm: {
        title: '',
        description: '',
        category: ''
      }
    }
  },

  setup() {
    const form = useForm({});

    return { form };
  },

  mounted() {
    this.initProfileForm();
  },

  methods: {
    initProfileForm() {
      this.profileForm = {
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        location: this.user.location || '',
        phone: this.user.phone || '',
        bio: this.user.bio || '',
        profile_image: null
      };
    },
    
    getInitials(firstName, lastName) {
      return `${firstName.charAt(0)}${lastName.charAt(0)}`;
    },
    
    handleImageUpload(event) {
      this.profileForm.profile_image = event.target.files[0];
    },
    
    updateProfile() {
      const formData = new FormData();
      formData.append('_method', 'PATCH');
      formData.append('first_name', this.profileForm.first_name);
      formData.append('last_name', this.profileForm.last_name);
      formData.append('email', this.profileForm.email);
      formData.append('location', this.profileForm.location);
      formData.append('phone', this.profileForm.phone);
      formData.append('bio', this.profileForm.bio);
      
      if (this.profileForm.profile_image) {
        formData.append('profile_image', this.profileForm.profile_image);
      }
      
      this.form.post(route('profile.update'), {
        data: formData,
        onSuccess: () => {
          this.editProfile = false;
        }
      });
    },
    
    editSkill(skill) {
      this.editingSkill = skill.id;
      this.skillForm = {
        title: skill.title,
        description: skill.description,
        category: skill.category
      };
      this.showAddSkillForm = true;
    },
    
    cancelSkillForm() {
      this.showAddSkillForm = false;
      this.editingSkill = null;
      this.skillForm = {
        title: '',
        description: '',
        category: ''
      };
    },
    
    saveSkill() {
      if (this.editingSkill) {
        // Update existing skill
        this.form.put(route('skills.update', this.editingSkill), {
          ...this.skillForm,
          onSuccess: () => {
            this.showAddSkillForm = false;
            this.editingSkill = null;
            this.skillForm = {
              title: '',
              description: '',
              category: ''
            };
          }
        });
      } else {
        // Create new skill
        this.form.post(route('skills.store'), {
          ...this.skillForm,
          onSuccess: () => {
            this.showAddSkillForm = false;
            this.skillForm = {
              title: '',
              description: '',
              category: ''
            };
          }
        });
      }
    },
    
    deleteSkill(skillId) {
      if (!confirm('Are you sure you want to delete this skill?')) return;
      
      this.form.delete(route('skills.destroy', skillId));
    },
    
    getStatusClass(status) {
      switch (status) {
        case 'pending':
          return 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded';
        case 'accepted':
          return 'bg-green-100 text-green-800 px-2 py-1 rounded';
        case 'rejected':
          return 'bg-red-100 text-red-800 px-2 py-1 rounded';
        default:
          return '';
      }
    },
    
    updateRequestStatus(requestId, status) {
      this.form.put(route('requests.update', requestId), {
        status: status
      });
    }
  }
});
</script>