<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100"
    >
        <div>
            <Link class="font-bold text-emerald-500" href="/">
                Let's Go To The Movies!
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
        <div class="py-12 flex flex-col items-center gap-5">
            <Link
                v-if="route().current('login')"
                class="underline"
                href="/register"
                >No Account? Register Here!
            </Link>
            <Link
                v-if="route().current('register')"
                class="underline"
                href="/login"
                >Go to Login
            </Link>
            <Link class="underline" href="/">Back to home </Link>
        </div>
    </div>
</template>
