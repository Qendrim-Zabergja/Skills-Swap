<template>
    <Head title="Log in" />
  
    <div class="min-h-screen flex items-center justify-center bg-white">
      <div class="max-w-md w-full px-6 py-8">
        <div class="text-center mb-6">
          <Link :href="route('home')" class="text-xl font-bold">SkillSwap</Link>
        </div>
  
        <div class="bg-white rounded-lg p-6">
          <h2 class="text-xl font-medium text-center mb-2">Log in to SkillSwap</h2>
          <p class="text-center text-gray-600 text-sm mb-6">Enter your email and password to access your account</p>
  
          <form @submit.prevent="submit">
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
              {{ status }}
            </div>
  
            <!-- Validation Errors -->
            <div v-if="form.errors" class="mb-4">
              <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600">
                {{ error }}
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
                autofocus
              />
            </div>
  
            <!-- Password -->
            <div class="mb-4">
              <div class="flex justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <Link
                  v-if="canResetPassword"
                  :href="route('password.request')"
                  class="text-sm text-gray-600 hover:underline"
                >
                  Forgot password?
                </Link>
              </div>
              <input
                id="password"
                type="password"
                v-model="form.password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
                required
                autocomplete="current-password"
              />
            </div>
  
            <!-- Remember Me -->
            <div class="mb-4">
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="form.remember"
                  class="rounded border-gray-300 text-black focus:ring-black"
                />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
              </label>
            </div>
  
            <!-- Login Button -->
            <button
              type="submit"
              class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black"
              :disabled="form.processing"
            >
              Log in
            </button>
          </form>
  
          <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
              Don't have an account? 
              <Link :href="route('register')" class="text-black hover:underline">
                Sign up
              </Link>
            </p>
          </div>
        </div>
  
        <div class="mt-8 text-center text-xs text-gray-500">
          <div>© 2023 SkillSwap. All rights reserved.</div>
          <div class="flex justify-center space-x-4 mt-2">
            <a href="#" class="hover:underline">About</a>
            <a href="#" class="hover:underline">Terms</a>
            <a href="#" class="hover:underline">Privacy</a>
            <a href="#" class="hover:underline">Cookie</a>
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
  
      const submit = () => {
        form.post(route('login'), {
          onFinish: () => form.reset('password'),
        });
      };
  
      return {
        form,
        submit,
      };
    },
  });
  </script>