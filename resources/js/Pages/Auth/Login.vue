<template>
  <Head title="Log in" />

  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="p-4 border-b">
      <div class="container mx-auto">
        <Link :href="route('home')" class="text-xl font-bold">SkillSwap</Link>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-12">
      <div class="w-full max-w-md px-6">
        <div class="bg-white rounded-lg border p-8">
          <h2 class="text-2xl font-semibold text-center mb-2">Log in to SkillSwap</h2>
          <p class="text-center text-gray-600 text-sm mb-6">Enter your email and password to access your account</p>

          <form @submit.prevent="validateAndSubmit">
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
              {{ status }}
            </div>

            <!-- Email -->
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input 
                id="email" 
                type="email" 
                v-model="form.email"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                :class="{'border-red-500': errors.email || form.errors.email, 'border-gray-300': !errors.email && !form.errors.email}"
                placeholder="name@example.com" 
                 
                autofocus 
              />
              <p v-if="errors.email || form.errors.email" class="mt-1 text-sm text-red-600">
                {{ errors.email || form.errors.email }}
              </p>
            </div>

            <!-- Password -->
            <div class="mb-6">
              <div class="flex justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <Link v-if="canResetPassword" :href="route('password.request')"
                  class="text-sm text-gray-600 hover:underline">
                Forgot password?
                </Link>
              </div>
              <input 
                id="password" 
                type="password" 
                v-model="form.password"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                :class="{'border-red-500': errors.password || form.errors.password, 'border-gray-300': !errors.password && !form.errors.password}"
                 
                autocomplete="current-password" 
              />
              <p v-if="errors.password || form.errors.password" class="mt-1 text-sm text-red-600">
                {{ errors.password || form.errors.password }}
              </p>
            </div>

            <!-- Remember Me -->
            <div class="mb-4 flex items-center">
              <input 
                id="remember" 
                type="checkbox" 
                v-model="form.remember"
                class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded"
              />
              <label for="remember" class="ml-2 block text-sm text-gray-700">
                Remember me
              </label>
            </div>

            <!-- Login Button -->
            <button 
              type="submit"
              class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Logging in...</span>
              <span v-else>Log in</span>
            </button>

            <!-- Sign Up Link -->
            <div class="text-center mt-6">
              <p class="text-sm text-gray-600">
                Don't have an account?
                <Link :href="route('register')" class="text-black hover:underline">
                Sign up
                </Link>
              </p>
            </div>
          </form>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 border-t">
      <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="text-sm text-gray-500 mb-4 md:mb-0">
            &copy; {{ new Date().getFullYear() }} SkillSwap. All rights reserved.
          </div>
          <div class="flex space-x-6">
            <a href="#" class="text-sm text-gray-500 hover:underline">About</a>
            <a href="#" class="text-sm text-gray-500 hover:underline">Terms</a>
            <a href="#" class="text-sm text-gray-500 hover:underline">Privacy</a>
            <a href="#" class="text-sm text-gray-500 hover:underline">Contact</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3';

export default defineComponent({
  components: {
    Head,
    Link,
  },

  props: {
    canResetPassword: Boolean,
    status: String,
  },

  setup() {
    const form = useForm({
      email: '',
      password: '',
      remember: false,
    });

    const errors = ref({});

    const validateForm = () => {
      errors.value = {};
      let isValid = true;

      // Validate email
      if (!form.email) {
        errors.value.email = 'Please enter your email address.';
        isValid = false;
      } else if (!validateEmail(form.email)) {
        errors.value.email = 'Please enter a valid email address.';
        isValid = false;
      }

      // Validate password
      if (!form.password) {
        errors.value.password = 'Please enter your password.';
        isValid = false;
      } else if (form.password.length < 8) {
        errors.value.password = 'Password must be at least 8 characters.';
        isValid = false;
      }

      return isValid;
    };

    const validateEmail = (email) => {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      return re.test(String(email).toLowerCase());
    };

    const validateAndSubmit = () => {
      // Clear previous errors but keep form data
      errors.value = {};
      form.clearErrors();
      
      if (validateForm()) {
        submit();
      }
    };

    const submit = () => {
      const formData = {
        email: form.email,
        password: form.password,
        remember: form.remember
      };

      form.post(route('login'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          window.location.href = route('home');
        },
        onError: (serverErrors) => {
          // Set server errors
          if (serverErrors.email) {
            errors.value.email = serverErrors.email;
          }
          if (serverErrors.password) {
            errors.value.password = serverErrors.password;
          }

          // Restore form data
          form.email = formData.email;
          form.password = formData.password;
          form.remember = formData.remember;
        }
      });
    };

    return {
      form,
      errors,
      validateAndSubmit,
    };
  },
});
</script>