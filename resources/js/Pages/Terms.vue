<template>
    <div class="min-h-screen flex flex-col bg-white">
        <Navbar />

        <main class="flex-grow">
            <section class="py-20 px-6 max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-6 text-center">Terms and Conditions</h1>
                <p class="text-gray-700 mb-4">
                    Welcome to SkillSwap. By using this platform, you agree to comply with and be bound by the following terms and conditions. 
                    Please read them carefully.
                </p>

                <h2 class="text-2xl font-semibold mt-8 mb-2">1. Acceptance of Terms</h2>
                <p class="text-gray-600 mb-4">
                    By accessing and using SkillSwap, you accept and agree to be bound by the terms and provisions of this agreement.
                </p>

                <h2 class="text-2xl font-semibold mt-8 mb-2">2. Skill Exchange Policy</h2>
                <p class="text-gray-600 mb-4">
                    SkillSwap is a non-monetary platform. Users agree to exchange skills without financial transactions. Misuse of this policy may result in account suspension.
                </p>

                <h2 class="text-2xl font-semibold mt-8 mb-2">3. User Responsibilities</h2>
                <p class="text-gray-600 mb-4">
                    Users are responsible for the content they share and the interactions they initiate. Offensive, harmful, or illegal behavior is strictly prohibited.
                </p>

                <h2 class="text-2xl font-semibold mt-8 mb-2">4. Privacy</h2>
                <p class="text-gray-600 mb-4">
                    Your privacy is important to us. Please review our Privacy Policy to understand how we collect and use your data.
                </p>

                <h2 class="text-2xl font-semibold mt-8 mb-2">5. Changes to Terms</h2>
                <p class="text-gray-600 mb-4">
                    We reserve the right to modify these terms at any time. Continued use of the platform implies acceptance of the updated terms.
                </p>
            </section>
        </main>

        <footer class="bg-gray-100 py-3 text-xs text-gray-500">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div>2025 SkillSwap. All rights reserved.</div>
                <div class="flex space-x-4">
                    <Link href="/about" class="hover:underline">About</Link>
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
