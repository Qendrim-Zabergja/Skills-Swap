<template>
    <div class="flex items-center">
        <template v-for="i in 5" :key="i">
            <svg 
                @click="handleClick(i)"
                @mouseover="hoverRating = i"
                @mouseleave="hoverRating = 0"
                :class="[
                    'w-5 h-5 cursor-pointer transition-colors',
                    i <= (hoverRating || modelValue) ? 'text-yellow-400' : 'text-gray-300',
                    'hover:scale-110'
                ]"
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 24 24" 
                fill="currentColor"
            >
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
            </svg>
        </template>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Number,
        required: true,
        default: 0
    }
});

const emit = defineEmits(['update:modelValue']);
const hoverRating = ref(0);

const handleClick = (value) => {
    emit('update:modelValue', value);
};
</script>
