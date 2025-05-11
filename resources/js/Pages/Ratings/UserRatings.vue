&lt;script setup&gt;
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
&lt;/script&gt;

&lt;template&gt;
    &lt;Head title="User Ratings" /&gt;

    &lt;AuthenticatedLayout&gt;
        &lt;template #header&gt;
            &lt;h2 class="font-semibold text-xl text-gray-800 leading-tight"&gt;
                User Ratings
            &lt;/h2&gt;
        &lt;/template&gt;

        &lt;div class="py-12"&gt;
            &lt;div class="max-w-7xl mx-auto sm:px-6 lg:px-8"&gt;
                &lt;div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"&gt;
                    &lt;div class="p-6"&gt;
                        &lt;div class="mb-6"&gt;
                            &lt;h3 class="text-lg font-semibold mb-2"&gt;Average Rating&lt;/h3&gt;
                            &lt;div class="flex items-center"&gt;
                                &lt;span class="text-2xl font-bold mr-2"&gt;{{ averageRating }}&lt;/span&gt;
                                &lt;div class="flex"&gt;
                                    &lt;StarIcon 
                                        v-for="i in 5" 
                                        :key="i"
                                        class="h-6 w-6"
                                        :class="i &lt;= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'"
                                    /&gt;
                                &lt;/div&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;

                        &lt;div class="space-y-4"&gt;
                            &lt;div v-for="rating in ratings.data" :key="rating.id" class="border rounded-lg p-4"&gt;
                                &lt;div class="flex items-center justify-between mb-2"&gt;
                                    &lt;div class="flex items-center"&gt;
                                        &lt;span class="font-semibold"&gt;{{ rating.rater.name }}&lt;/span&gt;
                                        &lt;div class="flex ml-4"&gt;
                                            &lt;StarIcon 
                                                v-for="i in 5" 
                                                :key="i"
                                                class="h-5 w-5"
                                                :class="i &lt;= rating.score ? 'text-yellow-400' : 'text-gray-300'"
                                            /&gt;
                                        &lt;/div&gt;
                                    &lt;/div&gt;
                                    &lt;span class="text-sm text-gray-500"&gt;
                                        {{ new Date(rating.created_at).toLocaleDateString() }}
                                    &lt;/span&gt;
                                &lt;/div&gt;
                                &lt;p v-if="rating.comment" class="text-gray-700"&gt;{{ rating.comment }}&lt;/p&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;

                        &lt;!-- Pagination --&gt;
                        &lt;div v-if="ratings.links.length &gt; 3" class="mt-6"&gt;
                            &lt;div class="flex justify-between"&gt;
                                &lt;Link
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
                                /&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/AuthenticatedLayout&gt;
&lt;/template&gt;
