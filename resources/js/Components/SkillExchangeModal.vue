<template>
  <Modal :show="show" @close="emit('close')" max-width="2xl">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-medium text-gray-900">
          Create Skill Exchange Request
        </h2>
        <button @click="emit('close')" class="text-gray-400 hover:text-gray-500">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="submitRequest" class="space-y-6">
        <!-- Exchange Details -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Skill You Want to Learn</label>
            <select v-model="form.skill_wanted" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">Select a skill you want to learn</option>
              <option v-for="skill in recipient.teaching_skills" :key="skill.id" :value="skill.name">
                {{ skill.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Skill You Can Teach</label>
            <select v-model="form.skill_offered" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">Select a skill you can teach</option>
              <option v-for="skill in user.teaching_skills" :key="skill.id" :value="skill.name">
                {{ skill.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
            <textarea 
              v-model="form.message"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
              placeholder="Introduce yourself and explain why you're interested in this skill exchange..."
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
              <select v-model="form.availability" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">Select availability</option>
                <option value="weekdays">Weekdays</option>
                <option value="weekends">Weekends</option>
                <option value="flexible">Flexible</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Session Duration</label>
              <select v-model="form.duration" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">Select duration</option>
                <option value="30">30 minutes</option>
                <option value="60">1 hour</option>
                <option value="90">1.5 hours</option>
                <option value="120">2 hours</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800"
            :disabled="!isFormValid"
          >
            Send Request
          </button>
        </div>
      </form>

      <!-- Recipient Info -->
      <div class="mt-8 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Request Recipient</h3>
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
            {{ getInitials(recipient.name) }}
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ recipient.name }}</h4>
            <p class="text-sm text-gray-500">{{ recipient.location }}</p>
            <div class="flex items-center mt-1">
              <span class="text-yellow-400 text-sm">★</span>
              <span class="text-sm text-gray-600 ml-1">{{ recipient.rating }} ({{ recipient.reviews_count }} reviews)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
  user: Object,
  recipient: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
  recipient_id: '',
  skill_wanted: '',
  skill_offered: '',
  message: '',
  availability: '',
  duration: ''
});

watch(() => props.recipient, (newRecipient) => {
  if (newRecipient) {
    form.recipient_id = newRecipient.id;
  }
}, { immediate: true });

const isFormValid = computed(() => {
  return form.skill_wanted && 
         form.skill_offered && 
         form.message && 
         form.availability && 
         form.duration;
});

const getInitials = (name) => {
  if (!name) return '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const submitRequest = () => {
  const requestData = {
    recipient_id: form.recipient_id,
    skill_wanted: form.skill_wanted,
    skill_offered: form.skill_offered,
    message: form.message,
    availability: form.availability,
    duration: form.duration
  };
  
  console.log('Submitting request:', requestData);
  
  form.post(route('requests.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('close');
      // Reload the page to show the new request
      window.location.reload();
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
    }
  });
};
</script>
