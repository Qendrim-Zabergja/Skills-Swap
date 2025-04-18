<template>
    <section class="max-w-7xl">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Skills Information</h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your skills that you can teach and those you want to learn.
            </p>
        </header>

        <form @submit.prevent="updateSkills" class="mt-6 space-y-6">
            <!-- Teaching Skills -->
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-4">Skills I Can Teach</h3>
                <div class="mb-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel :value="'Skill Title'" />
                                <TextInput
                                    v-model="newTeachingSkill.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel :value="'Category'" />
                                <select
                                    v-model="newTeachingSkill.category"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <InputLabel :value="'Description'" />
                            <textarea
                                v-model="newTeachingSkill.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                required
                            ></textarea>
                        </div>
                        <button 
                            type="button" 
                            @click="addTeachingSkill"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Add Teaching Skill
                        </button>
                    </div>
                </div>

                <!-- List of Teaching Skills -->
                <div v-if="form.teachingSkills.length > 0" class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-700">Added Teaching Skills:</h4>
                    <div v-for="(skill, index) in form.teachingSkills" :key="index" class="p-4 bg-white border rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium">{{ skill.title }}</h5>
                                <p class="text-sm text-gray-600">{{ skill.description }}</p>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">{{ skill.category }}</span>
                            </div>
                            <button 
                                type="button" 
                                @click="removeTeachingSkill(index)"
                                class="text-red-600 hover:text-red-800"
                            >
                                <span class="sr-only">Remove</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Skills -->
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-4">Skills I Want to Learn</h3>
                <div class="mb-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel :value="'Skill Title'" />
                                <TextInput
                                    v-model="newLearningSkill.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel :value="'Category'" />
                                <select
                                    v-model="newLearningSkill.category"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <InputLabel :value="'Description (Optional)'" />
                            <textarea
                                v-model="newLearningSkill.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                        </div>
                        <button 
                            type="button" 
                            @click="addLearningSkill"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Add Learning Skill
                        </button>
                    </div>
                </div>

                <!-- List of Learning Skills -->
                <div v-if="form.learningSkills.length > 0" class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-700">Added Learning Skills:</h4>
                    <div v-for="(skill, index) in form.learningSkills" :key="index" class="p-4 bg-white border rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium">{{ skill.title }}</h5>
                                <p class="text-sm text-gray-600">{{ skill.description }}</p>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">{{ skill.category }}</span>
                            </div>
                            <button 
                                type="button" 
                                @click="removeLearningSkill(index)"
                                class="text-red-600 hover:text-red-800"
                            >
                                <span class="sr-only">Remove</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save All Skills</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    skills: {
        type: Array,
        default: () => []
    }
});

const categories = [
    'Programming',
    'Design',
    'Music',
    'Languages',
    'Business',
    'Marketing',
    'Photography',
    'Writing',
    'Education',
    'Fitness',
    'Cooking',
    'Arts & Crafts'
];

const newTeachingSkill = ref({
    title: '',
    description: '',
    category: ''
});

const newLearningSkill = ref({
    title: '',
    description: '',
    category: ''
});

const form = useForm({
    teachingSkills: props.skills.filter(s => s.type === 'teaching').map(s => ({
        title: s.title,
        description: s.description,
        category: s.category
    })) || [],
    learningSkills: props.skills.filter(s => s.type === 'learning').map(s => ({
        title: s.title,
        description: s.description,
        category: s.category
    })) || []
});

const addTeachingSkill = () => {
    if (newTeachingSkill.value.title && newTeachingSkill.value.category) {
        form.teachingSkills.push({
            title: newTeachingSkill.value.title,
            description: newTeachingSkill.value.description,
            category: newTeachingSkill.value.category
        });
        // Reset form
        newTeachingSkill.value = {
            title: '',
            description: '',
            category: ''
        };
    }
};

const removeTeachingSkill = (index) => {
    form.teachingSkills.splice(index, 1);
};

const addLearningSkill = () => {
    if (newLearningSkill.value.title && newLearningSkill.value.category) {
        form.learningSkills.push({
            title: newLearningSkill.value.title,
            description: newLearningSkill.value.description,
            category: newLearningSkill.value.category
        });
        // Reset form
        newLearningSkill.value = {
            title: '',
            description: '',
            category: ''
        };
    }
};

const removeLearningSkill = (index) => {
    form.learningSkills.splice(index, 1);
};

const updateSkills = () => {
    form.post(route('skills.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // You can add success handling here
        }
    });
};
</script>
