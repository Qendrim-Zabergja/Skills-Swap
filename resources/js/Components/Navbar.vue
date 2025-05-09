<template>
    <header class="border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <Link :href="route('home')" class="font-bold text-lg">SkillSwap</Link>
            <Link :href="route('skills.browse')" class="mr-4 text-sm font-bold">Browse Skills</Link>

            <div v-if="$page.props.auth?.user" class="flex items-center space-x-4">
                <Link :href="route('profile.edit')" class="flex items-center space-x-2">
                    <div v-if="$page.props.auth?.user?.profile_photo_url" class="w-8 h-8 rounded-full overflow-hidden">
                        <img :src="$page.props.auth?.user?.profile_photo_url" alt="Profile photo" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-sm">
                        {{ getInitials($page.props.auth?.user?.name) }}
                    </div>
                    <span class="text-sm">My Profile</span>
                </Link>
                <Link :href="route('logout')" method="post" as="button"
                    class="bg-black text-white px-3 py-1 text-sm rounded hover:bg-gray-800">
                    Log out
                </Link>
            </div>
            <div v-else>
                <Link :href="route('login')" class="mr-4 text-sm">Log in</Link>
                <Link :href="route('register')" class="bg-black text-white px-3 py-1 text-sm rounded">Sign up</Link>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};
</script>
