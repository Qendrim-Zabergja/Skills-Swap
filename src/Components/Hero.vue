<script setup lang="ts">
import { ref } from 'vue';

const searchQuery = ref('');
</script>

<template>
  <div class="bg-gray-50 py-20">
    <div class="container mx-auto px-4 text-center">
      <h1 class="text-5xl font-bold mb-6">Swap Skills, Learn & Teach for Free!</h1>
      <p class="text-xl text-gray-600 mb-12">
        Exchange your expertise with others. No money involved - just knowledge sharing.
      </p>
      <div class="max-w-2xl mx-auto flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search skills (e.g., Graphic Design)"
          class="flex-1 px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-black"
        />
        <button class="btn btn-primary px-8">Find Skills</button>
      </div>
    </div>
  </div>
</template>