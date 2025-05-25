<template>
  <div class="flex flex-col min-h-screen bg-white">
    <Navbar />

    <main class="flex-grow">
      <section class="py-20 px-6 max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-6">Contact Us</h1>
        <p class="text-gray-700 text-lg mb-4">
          We'd love to hear from you! If you have any questions, feedback, or collaboration ideas, feel free to reach out.
        </p>
        <p class="text-gray-600 mb-2">
          📧 Email: <a href="mailto:contact@skillswap.com" class="text-blue-600 hover:underline">contact@skillswap.com</a>
        </p>
        <p class="text-gray-600">
          🏢 Address: 123 SkillSwap Lane, Creators City, LearnState, 45678
        </p>
      </section>
    </main>

    <footer class="bg-gray-100 py-3 text-xs text-gray-500">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <div>2025 SkillSwap. All rights reserved.</div>
        <div class="flex space-x-4">
          <Link href="/about" class="underline">About</Link>
          <Link href="/terms" class="underline">Terms</Link>
          <Link href="/privacy" class="underline">Privacy</Link>
          <Link href="/contact" class="underline">Contact</Link>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import Navbar from '../Components/Navbar.vue';
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Navbar,
    Link,
  },
};
</script>
