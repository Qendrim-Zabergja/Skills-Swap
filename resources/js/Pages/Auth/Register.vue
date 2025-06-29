<template>

  <Head title="Register" />

  <div class="min-h-screen flex items-center justify-center bg-white">
    <div class="max-w-md w-full px-6 py-8">
      <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-2">Create an account</h2>
        <p class="text-center text-gray-600 mb-6">Join SkillSwap to start exchanging skills with others</p>

        <form @submit.prevent="submit">
          <!-- Validation Errors -->
          <div v-if="Object.keys(errors).length > 0" class="mb-4">
            <div v-if="errors.general" class="text-sm text-red-600 mb-2">
              {{ errors.general }}
            </div>
          </div>

          <!-- Name Fields -->
          <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- First Name -->
            <div class="mb-4">
              <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                First Name <span class="text-red-600">*</span>
              </label>
              <input id="first_name" type="text" v-model="form.first_name"
                :class="{
                  'border-red-500': errors.first_name, 
                  'border-gray-300': !errors.first_name,
                  'focus:ring-red-500': errors.first_name,
                  'focus:ring-black': !errors.first_name
                }"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2"
                :disabled="isLoading"
                autofocus autocomplete="given-name" />
              <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                <span class="font-medium">⚠</span> {{ errors.first_name }}
              </p>
            </div>
            <!-- Last Name -->
            <div class="mb-4">
              <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                Last Name <span class="text-red-600">*</span>
              </label>
              <input id="last_name" type="text" v-model="form.last_name"
                :class="{
                  'border-red-500': errors.last_name, 
                  'border-gray-300': !errors.last_name,
                  'focus:ring-red-500': errors.last_name,
                  'focus:ring-black': !errors.last_name
                }"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2"
                :disabled="isLoading"
                autocomplete="family-name" />
              <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                <span class="font-medium">⚠</span> {{ errors.last_name }}
              </p>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Email <span class="text-red-600">*</span>
            </label>
            <input id="email" type="email" v-model="form.email"
              :class="{
                'border-red-500': errors.email, 
                'border-gray-300': !errors.email,
                'focus:ring-red-500': errors.email,
                'focus:ring-black': !errors.email
              }"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2"
              :disabled="isLoading"
              autocomplete="email" />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
              <span class="font-medium">⚠</span> {{ errors.email }}
            </p>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
              Password <span class="text-red-600">*</span>
            </label>
            <input id="password" type="password" v-model="form.password"
              :class="{
                'border-red-500': errors.password, 
                'border-gray-300': !errors.password,
                'focus:ring-red-500': errors.password,
                'focus:ring-black': !errors.password
              }"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2"
              :disabled="isLoading"
               autocomplete="new-password" />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">
              <span class="font-medium">⚠</span> {{ errors.password }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              Password must be at least 8 characters long and include:
              <span :class="{'text-red-600': errors.password}">
                one uppercase letter, one lowercase letter, one number, and one special character.
              </span>
            </p>
          </div>

          <!-- Confirm Password -->
          <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
              Confirm Password <span class="text-red-600">*</span>
            </label>
            <input id="password_confirmation" type="password" v-model="form.password_confirmation"
              :class="{
                'border-red-500': errors.password_confirmation, 
                'border-gray-300': !errors.password_confirmation,
                'focus:ring-red-500': errors.password_confirmation,
                'focus:ring-black': !errors.password_confirmation
              }"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2"
              :disabled="isLoading"
               autocomplete="new-password" />
            <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
              <span class="font-medium">⚠</span> {{ errors.password_confirmation }}
            </p>
          </div>

          <!-- Terms and Conditions -->
          <div class="mb-6">
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input id="terms" type="checkbox" v-model="form.terms"
                  :class="{
                    'border-red-500': errors.terms,
                    'border-gray-300': !errors.terms,
                    'focus:ring-red-500': errors.terms,
                    'focus:ring-black': !errors.terms
                  }"
                  class="h-4 w-4 text-black rounded focus:ring-2 focus:ring-offset-2" />
              </div>
              <div class="ml-3">
                <label for="terms" class="block text-sm text-gray-700">
                  I agree to the 
                  <a href="#" @click.prevent="showTermsModal = true" class="font-medium text-black hover:underline">
                    Terms of Service
                  </a> 
                  and 
                  <a href="#" @click.prevent="showPrivacyModal = true" class="font-medium text-black hover:underline">
                    Privacy Policy
                  </a>
                  <span class="text-red-600">*</span>
                </label>
                <p v-if="errors.terms" class="mt-1 text-sm text-red-600">
                  <span class="font-medium">⚠</span> {{ errors.terms }}
                </p>
              </div>
            </div>
          </div>
          

          <!-- Submit Button -->
          <button type="submit"
            class="w-full bg-black text-white font-semibold py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black disabled:opacity-50 flex justify-center items-center"
            :disabled="isLoading">
            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? 'Creating Account...' : 'Sign Up' }}
          </button>
        </form>

        <!-- Already have an account? -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <Link href="/login" class="text-black hover:underline font-medium">
            Log in
            </Link>
          </p>
        </div>
      </div>
    </div>
  </div>
  <!-- Terms Modal -->
<div v-if="showTermsModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-xl max-w-lg w-full shadow-lg max-h-[80vh] overflow-y-auto">
    <h3 class="text-xl font-bold mb-4">Terms of Service</h3>
    <p class="text-sm text-gray-700 mb-3">
      Welcome to SkillSwap! By accessing or using our platform, you agree to comply with and be bound by these Terms of Service. Please read them carefully.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      1. <strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      2. <strong>User Conduct:</strong> You agree not to use SkillSwap for any unlawful or prohibited activities. Respect other users and refrain from harassment, discrimination, or harmful behavior.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      3. <strong>Content Ownership:</strong> You retain ownership of your content, but grant SkillSwap a license to display and distribute it within the platform.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      4. <strong>Modifications:</strong> We may update or modify these Terms from time to time. Continued use of the service constitutes acceptance of those changes.
    </p>
    <p class="text-sm text-gray-700">
      For full details, please visit our website or contact support for any questions.
    </p>
    <div class="mt-6 text-right">
      <button @click="showTermsModal = false"
              class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
        Close
      </button>
    </div>
  </div>
</div>
       <!-- Privacy Policy Modal -->
<div v-if="showPrivacyModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-xl max-w-lg w-full shadow-lg max-h-[80vh] overflow-y-auto">
    <h3 class="text-xl font-bold mb-4">Privacy Policy</h3>
    <p class="text-sm text-gray-700 mb-3">
      At SkillSwap, we value your privacy and are committed to protecting your personal information.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      1. <strong>Information Collection:</strong> We collect personal data that you provide during registration and use of our services, including your name, email, and profile details.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      2. <strong>Use of Data:</strong> Your data is used to provide and improve our services, communicate updates, and personalize your experience.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      3. <strong>Data Sharing:</strong> We do not sell your information to third parties. We may share data with trusted service providers under strict confidentiality agreements.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      4. <strong>Security:</strong> We implement appropriate technical and organizational measures to safeguard your information.
    </p>
    <p class="text-sm text-gray-700 mb-3">
      5. <strong>Your Rights:</strong> You can access, correct, or request deletion of your personal data by contacting our support team.
    </p>
    <p class="text-sm text-gray-700">
      For more details, please refer to our full Privacy Policy on the website.
    </p>
    <div class="mt-6 text-right">
      <button @click="showPrivacyModal = false"
              class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
        Close
      </button>
    </div>
  </div>
</div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

export default defineComponent({
  components: {
    Head,
    Link,
  },
  setup() {
    const form = useForm({
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      password_confirmation: '',
      terms: false,
    });

    const isLoading = ref(false);
    const errors = ref({});

       // Modal state refs
    const showTermsModal = ref(false);
    const showPrivacyModal = ref(false);

    const submit = async () => {
      isLoading.value = true;
      errors.value = {};

      console.log('Submitting registration with data:', {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
        terms: form.terms,
      });

      try {
        const response = await axios.post(route('register'), {
          name: `${form.first_name} ${form.last_name}`,
          first_name: form.first_name,
          last_name: form.last_name,
          email: form.email,
          password: form.password,
          password_confirmation: form.password_confirmation,
          terms: form.terms,
        });

        console.log('Registration successful, redirecting...');
        // If successful, the server will redirect
        window.location.href = route('home');
      } catch (error) {
        console.error('Registration error:', error);
        console.error('Error response:', error.response?.data);

        if (error.response?.status === 422) {
          // Handle validation errors
          const validationErrors = error.response.data.errors || {};
          const formattedErrors = {};

          console.log('Validation errors:', validationErrors);

          // Format the errors to ensure they're in the correct format
          Object.keys(validationErrors).forEach(field => {
            // Take the first error message for each field
            formattedErrors[field] = Array.isArray(validationErrors[field])
              ? validationErrors[field][0]
              : validationErrors[field];
          });

          console.log('Formatted errors:', formattedErrors);
          errors.value = formattedErrors;
        } else {
          // Handle other types of errors
          const errorMessage = error.response?.data?.message || 
                             error.response?.data?.error || 
                             'An error occurred during registration. Please try again.';
          
          console.error('Registration failed:', errorMessage);
          errors.value.general = errorMessage;
        }
      } finally {
        isLoading.value = false;
      }
    };

    return {
      form,
      errors,
      isLoading,
      submit,
      showTermsModal,
      showPrivacyModal,
    };
  },
});
</script>