<template>
    <header class="border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <Link :href="route('home')" class="font-bold text-lg">SkillSwap</Link>
            <Link :href="route('skills.browse')" class="mr-4 text-sm font-bold">Browse Skills</Link>

            <div v-if="$page.props.auth.user">
                <Link :href="route('profile.edit')" class="mr-4 text-sm">My Profile</Link>
                <Link :href="route('logout')" method="post" as="button"
                    class="bg-black text-white px-3 py-1 text-sm rounded">
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

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link
    }
}
</script>
