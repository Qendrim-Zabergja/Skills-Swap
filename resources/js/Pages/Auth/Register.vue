<template>
  <Head title="Register" />

  <div class="min-h-screen flex items-center justify-center bg-white">
    <div class="max-w-md w-full px-6 py-8">
      <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-2">Create an account</h2>
        <p class="text-center text-gray-600 mb-6">Join SkillSwap to start exchanging skills with others</p>

        <form @submit.prevent="submit">
          <!-- Validation Errors -->
          <div v-if="form.errors && Object.keys(form.errors).length > 0" class="mb-4">
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
            <p class="text-xs text-gray-500 mt-1">
              Password must be at least 8 characters long and include a number and a special character.
            </p>
          </div>

          <!-- Confirm Password -->
          <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
              Confirm Password
            </label>
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
          <div class="flex items-start mb-6">
            <input 
              id="terms" 
              type="checkbox" 
              v-model="termsAccepted"
              class="h-4 w-4 text-black border-gray-300 rounded focus:ring-black mt-0.5"
              required 
            />
            <label for="terms" class="ml-2 block text-sm text-gray-700">
              I agree to the 
              <button 
                type="button" 
                @click="showTermsModal = true" 
                class="font-medium text-black hover:underline focus:outline-none focus:underline"
              >
                Terms of Service
              </button> 
              and 
              <button 
                type="button" 
                @click="showPrivacyModal = true"
                class="font-medium text-black hover:underline focus:outline-none focus:underline"
              >
                Privacy Policy
              </button>
            </label>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit"
            class="w-full bg-black text-white font-semibold py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black disabled:opacity-50"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Creating Account...</span>
            <span v-else>Sign Up</span>
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

  <!-- Terms of Service Modal -->
  <Teleport to="body">
    <div 
      v-if="showTermsModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="showTermsModal = false"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] flex flex-col">
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Terms of Service</h3>
            <button 
              @click="showTermsModal = false" 
              class="text-gray-400 hover:text-gray-600 focus:outline-none"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <p class="text-sm text-gray-600 mt-1">Please read our terms of service carefully.</p>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
          <div class="space-y-4 text-sm">
            <section>
              <h4 class="font-semibold text-base mb-2">1. Acceptance of Terms</h4>
              <p>By accessing and using SkillSwap, you accept and agree to be bound by the terms and provision of this agreement.</p>
            </section>

            <section>
              <h4 class="font-semibold text-base mb-2">2. Use License</h4>
              <p>Permission is granted to temporarily download one copy of SkillSwap per device for personal, non-commercial transitory viewing only.</p>
            </section>

            <section>
              <h4 class="font-semibold text-base mb-2">3. Skill Exchange Guidelines</h4>
              <p>Users must provide accurate information about their skills and availability. All skill exchanges should be conducted in good faith and with mutual respect.</p>
            </section>

            <section>
              <h4 class="font-semibold text-base mb-2">4. User Conduct</h4>
              <p>Users agree not to use the service for any unlawful purpose or to solicit others to perform unlawful acts.</p>
            </section>
          </div>
        </div>
        
        <div class="p-6 border-t border-gray-200">
          <button 
            @click="showTermsModal = false" 
            class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </Teleport>

  <!-- Privacy Policy Modal -->
  <Teleport to="body">
    <div 
      v-if="showPrivacyModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="showPrivacyModal = false"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] flex flex-col">
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Privacy Policy</h3>
            <button 
              @click="showPrivacyModal = false" 
              class="text-gray-400 hover:text-gray-600 focus:outline-none"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <p class="text-sm text-gray-600 mt-1">Learn how we collect, use, and protect your personal information.</p>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
          <div class="space-y-4 text-sm">
            <section>
              <h4 class="font-semibold text-base mb-2">Information We Collect</h4>
              <p>We collect information you provide directly to us, such as when you create an account, update your profile, or contact us for support.</p>
            </section>

            <section>
              <h4 class="font-semibold text-base mb-2">How We Use Your Information</h4>
              <p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
            </section>

            <section>
              <h4 class="font-semibold text-base mb-2">Data Security</h4>
              <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            </section>
          </div>
        </div>
        
        <div class="p-6 border-t border-gray-200">
          <button 
            @click="showPrivacyModal = false" 
            class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { useForm } from '@inertiajs/inertia-vue3'

export default defineComponent({
  components: {
    Head,
    Link,
  },
  setup() {
    const showTermsModal = ref(false)
    const showPrivacyModal = ref(false)
    const termsAccepted = ref(false)

    const form = useForm({
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      password_confirmation: '',
    })

    const submit = () => {
      // Validate terms acceptance
      if (!termsAccepted.value) {
        alert('Please agree to the Terms of Service and Privacy Policy')
        return
      }

      // Transform data and submit
      form.transform((data) => ({
        ...data,
        name: `${data.first_name} ${data.last_name}`,
        terms_accepted: termsAccepted.value,
      })).post('/register', {
        onFinish: () => {
          form.reset('password', 'password_confirmation')
        },
        onError: (errors) => {
          console.error('Registration errors:', errors)
        }
      })
    }

    return {
      form,
      submit,
      showTermsModal,
      showPrivacyModal,
      termsAccepted,
    }
  },
})
</script>