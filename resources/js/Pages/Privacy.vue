<template>
  <div class="flex flex-col min-h-screen bg-white">
    <Navbar />

    <main class="flex-grow">
      <section class="py-20 px-6 max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-6">Privacy Policy</h1>
        <p class="text-gray-700 text-lg mb-4">
          At SkillSwap, we take your privacy seriously. This Privacy Policy outlines the types of personal information we collect,
          how we use it, and how we protect it.
        </p>
        <p class="text-gray-600 mb-4">
          We only collect the data necessary to provide and improve our services. Your data is never sold or shared without your
          consent.
        </p>
        <p class="text-gray-600">
          By using SkillSwap, you agree to the terms of this Privacy Policy. If you have any questions, please contact us at
          privacy@skillswap.com.
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
