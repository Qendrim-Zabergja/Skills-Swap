<template>
  <Modal :show="show" @close="$emit('close')" max-width="2xl">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-medium text-gray-900">
          Edit Profile
        </h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="validateAndSave" class="space-y-6">
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
              :class="{'border-red-500': errors.name}"
            >
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
              :class="{'border-red-500': errors.email}"
            >
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input 
              v-model="form.phone" 
              type="tel" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
              :class="{'border-red-500': errors.phone}"
            >
            <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input 
              v-model="form.location" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
              :class="{'border-red-500': errors.location}"
            >
            <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
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
              @keyup.enter.prevent="addTeachingSkill"
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
          <p v-if="errors.teachingSkills" class="mt-1 text-sm text-red-600">{{ errors.teachingSkills }}</p>
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
              @keyup.enter.prevent="addLearningSkill"
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
          <p v-if="errors.learningSkills" class="mt-1 text-sm text-red-600">{{ errors.learningSkills }}</p>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6">
          <button
            type="button"
            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
            @click="$emit('close')"
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
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

export default {
  components: {
    Modal
  },

  props: {
    show: Boolean,
    user: Object,
    teachingSkills: Array,
    learningSkills: Array
  },

  data() {
    return {
      form: useForm({
        name: this.user?.name || '',
        email: this.user?.email || '',
        phone: this.user?.phone || '',
        location: this.user?.location || '',
        teachingSkills: this.teachingSkills || [],
        learningSkills: this.learningSkills || []
      }),
      errors: {},
      newTeachingSkill: '',
      newTeachingCategory: '',
      newLearningSkill: '',
      newLearningCategory: '',
      categories: [
        'Programming',
        'Design',
        'Marketing',
        'Business',
        'Languages',
        'Music',
        'Photography',
        'Other'
      ]
    }
  },

  methods: {
    getInitials(name) {
      if (!name) return '';
      return name.split(' ').map(n => n[0]).join('').toUpperCase();
    },

    addTeachingSkill() {
      if (this.newTeachingSkill.trim() && this.newTeachingCategory) {
        this.form.teachingSkills.push({
          name: this.newTeachingSkill.trim(),
          category: this.newTeachingCategory
        });
        this.newTeachingSkill = '';
        this.newTeachingCategory = '';
        // Clear error if it exists
        if (this.errors.teachingSkills) {
          delete this.errors.teachingSkills;
        }
      }
    },

    removeTeachingSkill(skill) {
      this.form.teachingSkills = this.form.teachingSkills.filter(s => s.name !== skill.name);
    },

    addLearningSkill() {
      if (this.newLearningSkill.trim() && this.newLearningCategory) {
        this.form.learningSkills.push({
          name: this.newLearningSkill.trim(),
          category: this.newLearningCategory
        });
        this.newLearningSkill = '';
        this.newLearningCategory = '';
        // Clear error if it exists
        if (this.errors.learningSkills) {
          delete this.errors.learningSkills;
        }
      }
    },

    removeLearningSkill(skill) {
      this.form.learningSkills = this.form.learningSkills.filter(s => s.name !== skill.name);
    },

    validateForm() {
      this.errors = {};
      let isValid = true;

      // Validate required fields
      if (!this.form.name) {
        this.errors.name = 'Field is required';
        isValid = false;
      }

      if (!this.form.email) {
        this.errors.email = 'Field is required';
        isValid = false;
      } else if (!this.validateEmail(this.form.email)) {
        this.errors.email = 'Please enter a valid email address';
        isValid = false;
      }

      if (!this.form.phone) {
        this.errors.phone = 'Field is required';
        isValid = false;
      }

      if (!this.form.location) {
        this.errors.location = 'Field is required';
        isValid = false;
      }

      // Check if skills are required
      if (this.form.teachingSkills.length === 0) {
        this.errors.teachingSkills = 'Please add at least one skill you can teach';
        isValid = false;
      }

      if (this.form.learningSkills.length === 0) {
        this.errors.learningSkills = 'Please add at least one skill you want to learn';
        isValid = false;
      }

      return isValid;
    },

    validateEmail(email) {
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    },

    validateAndSave() {
      if (this.validateForm()) {
        this.saveProfile();
      }
    },

    saveProfile() {
      this.form.patch(route('profile.update'), {
        onSuccess: () => {
          this.$emit('saved');
        },
        onError: (errors) => {
          // Handle server-side validation errors if they come back
          this.errors = errors;
        }
      });
    }
  }
};
</script>