<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { StarIcon } from '@heroicons/vue/24/solid';

defineProps({
    ratings: {
        type: Object,
        required: true
    },
    averageRating: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <Head title="User Ratings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Ratings
            </h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Average Rating</h3>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold mr-2">{{ averageRating }}</span>
                                <div class="flex">
                                    <StarIcon 
                                        v-for="i in 5" 
                                        :key="i"
                                        class="h-6 w-6"
                                        :class="i <= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'"
                                    />
                                </div>
                            </div>
                        </div>


                        <div class="space-y-4">
                            <div v-for="rating in ratings.data" :key="rating.id" class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <span class="font-semibold">{{ rating.rater.name }}</span>
                                        <div class="flex ml-4">
                                            <StarIcon 
                                                v-for="i in 5" 
                                                :key="i"
                                                class="h-5 w-5"
                                                :class="i <= rating.score ? 'text-yellow-400' : 'text-gray-300'"
                                            />
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ new Date(rating.created_at).toLocaleDateString() }}
                                    </span>
                                </div>
                                <p v-if="rating.comment" class="text-gray-700">{{ rating.comment }}</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="ratings.links.length > 3" class="mt-6">
                            <div class="flex justify-between">
                                <Link
                                    v-for="link in ratings.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-4 py-2 border rounded"
                                    :class="{
                                        'bg-gray-200': link.active,
                                        'text-gray-400': !link.url,
                                        'hover:bg-gray-50': link.url
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
