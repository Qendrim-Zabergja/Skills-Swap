<script setup lang="ts">
import Navbar from './Components/Navbar.vue';
import Hero from './Components/Hero.vue';
import FeaturedSkills from './Components/FeaturesSkills.vue';
import HowItWorks from './Components/HowItWorks.vue';
</script>

<template>
  <div class="min-h-screen bg-white">
    <Navbar />
    <main>
      <Hero />
      <FeaturedSkills />
      <HowItWorks />
    </main>
    <footer class="bg-gray-50 py-8 mt-20">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
          <p class="text-gray-600">© 2025 SkillSwap. All rights reserved.</p>
          <div class="space-x-6">
            <a href="#" class="text-gray-600 hover:text-gray-900">About</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">Terms</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">Privacy</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">Contact</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>