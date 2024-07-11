<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
const { auth } = usePage().props;
defineProps({
    favorites: Array,
});
</script>

<template>
    <div class="container mx-auto px-4">
        <header class="py-20">
            <nav>
                <Link href="/" class="text-white underline">back home</Link>
            </nav>
            <h1 class="py-10 text-5xl text-white capitalize">
                {{ auth.user.name }}'s Favorite Movies
            </h1>
        </header>
        <main>
            <div class="grid grid-cols-4 gap-10 pb-20">
                <Link
                    v-for="movie in favorites"
                    :href="`/movie/${movie.movie_id}`"
                    :key="movie.movie_id"
                >
                    <div class="flex flex-col gap-4 items-center">
                        <img :src="movie.poster_url" :alt="movie.title" />
                        <h2 class="text-white font-bold text-2xl">
                            {{ movie.title }}
                        </h2>
                    </div>
                </Link>
            </div>
        </main>
    </div>
</template>

<style scoped></style>
