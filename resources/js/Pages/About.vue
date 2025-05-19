<template>
    <div class="min-h-screen flex flex-col bg-white">
        <Navbar />

        <!-- Main Content -->
        <main class="flex-grow">
            <section class="py-20 px-6 max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-6">About SkillSwap</h1>
                <p class="text-gray-700 text-lg mb-4">
                    SkillSwap is a community platform where people can exchange skills and knowledge without any monetary transactions.
                    We believe everyone has something to teach and something to learn.
                </p>
                <p class="text-gray-600">
                    Our mission is to foster a learning environment driven by collaboration and passion. Whether you’re into tech,
                    art, music, or anything else — join the platform and start swapping skills today!
                </p>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 py-3 text-xs text-gray-500">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div>2025 SkillSwap. All rights reserved.</div>
                <div class="flex space-x-4">
                    <Link href="/about" class="underline">About</Link>
                    <Link href="/terms" class="underline">Terms</Link>
                    <Link href="/privacy" class="underline">Privacy</Link>
                    <Link href="/contact" class="underline">Contact</Link>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import Navbar from '../Components/Navbar.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Navbar,
        Link,
    }
};
</script>
