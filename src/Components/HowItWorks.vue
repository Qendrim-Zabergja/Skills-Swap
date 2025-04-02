<script setup lang="ts">
const steps = [
  {
    number: 1,
    title: 'List Your Skills',
    description: 'Create a profile and add the skills you can teach others.'
  },
  {
    number: 2,
    title: 'Find & Connect',
    description: 'Browse skills you want to learn and connect with potential teachers.'
  },
  {
    number: 3,
    title: 'Swap & Learn',
    description: 'Exchange your knowledge and grow your skillset without spending money.'
  }
];
</script>

<template>
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-16">How SkillSwap Works</h2>
      
      <div class="grid md:grid-cols-3 gap-12">
        <div
          v-for="step in steps"
          :key="step.number"
          class="text-center"
        >
          <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
            {{ step.number }}
          </div>
          <h3 class="text-xl font-semibold mb-4">{{ step.title }}</h3>
          <p class="text-gray-600">{{ step.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>