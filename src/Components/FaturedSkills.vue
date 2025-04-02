<script setup lang="ts">
const featuredSkills = [
  {
    id: 1,
    name: 'Alex Morgan',
    location: 'New York, USA',
    category: 'Design',
    title: 'Graphic Design & Brand Identity',
    description: 'I can teach you how to create stunning logos, brand identities, and marketing materials using Adobe Creative Suite.',
    rating: 4.9,
    swaps: 24,
    lookingFor: ['Web Development', 'Digital Marketing']
  },
  {
    id: 2,
    name: 'Sarah Chen',
    location: 'Toronto, Canada',
    category: 'Development',
    title: 'Full-Stack Web Development',
    description: 'Learn how to build responsive websites and web applications using React, Node.js, and modern web technologies.',
    rating: 4.8,
    swaps: 18,
    lookingFor: ['Photography', 'UI/UX Design']
  },
  {
    id: 3,
    name: 'Miguel Rodriguez',
    location: 'Barcelona, Spain',
    category: 'Music',
    title: 'Guitar Lessons for Beginners',
    description: "I'll teach you to play guitar from scratch - chords, strumming patterns, and your favorite songs in just a few sessions.",
    rating: 4.7,
    swaps: 31,
    lookingFor: ['Language (English)', 'Cooking']
  }
];
</script>

<template>
  <section class="py-20">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-12">Featured Skill Offers</h2>
      <div class="grid md:grid-cols-3 gap-8">
        <div
          v-for="skill in featuredSkills"
          :key="skill.id"
          class="bg-white rounded-lg border p-6 hover:shadow-lg transition-shadow"
        >
          <div class="flex items-start gap-4 mb-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
            <div>
              <h3 class="font-semibold">{{ skill.name }}</h3>
              <p class="text-gray-600 text-sm">{{ skill.location }}</p>
            </div>
            <span class="ml-auto text-sm font-medium">{{ skill.category }}</span>
          </div>
          
          <h4 class="text-xl font-semibold mb-2">{{ skill.title }}</h4>
          <p class="text-gray-600 mb-4">{{ skill.description }}</p>
          
          <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(interest, index) in skill.lookingFor"
                :key="index"
                class="text-sm bg-gray-100 px-3 py-1 rounded-full"
              >
                Looking for: {{ interest }}
              </span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1">
                <span class="text-yellow-400">★</span>
                <span class="font-medium">{{ skill.rating }}</span>
                <span class="text-gray-600">• {{ skill.swaps }} swaps</span>
              </div>
              <button class="btn btn-primary">Request Swap</button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="text-center mt-12">
        <button class="btn btn-outline">View All Skills</button>
      </div>
    </div>
  </section>
</template>