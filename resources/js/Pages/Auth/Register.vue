<template>
    <Head title="Register" />
  
    <div class="min-h-screen flex items-center justify-center bg-white">
      <div class="max-w-md w-full px-6 py-8">
        <div class="bg-white rounded-xl shadow-md p-8">
          <h2 class="text-2xl font-bold text-center mb-2">Create an account</h2>
          <p class="text-center text-gray-600 mb-6">Join SkillSwap to start exchanging skills with others</p>
  
          <form @submit.prevent="submit">
            <!-- Validation Errors -->
            <div v-if="form.errors" class="mb-4">
              <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600">
                {{ error }}
              </div>
            </div>
  
            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input
                  id="first_name"
                  type="text"
                  v-model="form.first_name"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                  required
                  autofocus
                  autocomplete="given-name"
                />
              </div>
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input
                  id="last_name"
                  type="text"
                  v-model="form.last_name"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                  required
                  autocomplete="family-name"
                />
              </div>
            </div>
  
            <!-- Email -->
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                id="email"
                type="email"
                v-model="form.email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                required
                autocomplete="email"
              />
            </div>
  
            <!-- Password -->
            <div class="mb-4">
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input
                id="password"
                type="password"
                v-model="form.password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                required
                autocomplete="new-password"
              />
              <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters long and include a number and a special character.</p>
            </div>
  
            <!-- Confirm Password -->
            <div class="mb-4">
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
              <input
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                required
                autocomplete="new-password"
              />
            </div>
  
            <!-- Terms and Conditions -->
            <div class="flex items-center mb-6">
              <input
                id="terms"
                type="checkbox"
                class="h-4 w-4 text-black border-gray-300 rounded focus:ring-black"
                required
              />
              <label for="terms" class="ml-2 block text-sm text-gray-700">
                I agree to the <a href="#" class="font-medium text-black hover:underline">Terms of Service</a> and <a href="#" class="font-medium text-black hover:underline">Privacy Policy</a>
              </label>
            </div>
  
            <!-- Submit Button -->
            <button
              type="submit"
              class="w-full bg-black text-white font-semibold py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black"
              :disabled="form.processing"
            >
              Sign Up
            </button>
          </form>
  
          <!-- Already have an account? -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Already have an account?
              <Link
                href="/login"
                class="text-black hover:underline font-medium"
              >
                Log in
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { defineComponent } from 'vue';
  import { Head, Link } from '@inertiajs/inertia-vue3';
  import { useForm } from '@inertiajs/inertia-vue3';
  
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
      });
  
      const submit = () => {
        form.post(route('register'), {
          onFinish: () => form.reset('password', 'password_confirmation'),
        });
      };
  
      return {
        form,
        submit,
      };
    },
  });
  </script>
  