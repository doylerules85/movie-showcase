<script setup lang="ts">
import { FilmIcon } from "@heroicons/vue/24/outline";
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const { auth, flash }: any = usePage().props;

const props = defineProps({
    id: String,
});

type Movie = {
    id: string;
    posterUrl?: string;
    title: string;
    summary: string;
    rating: string;
    genres: { title: string; id: string }[];
};

const movie = ref<Movie>();
const movieError = ref<string>("");
const loading = ref(true);

const getMovies = async () => {
    let token = window.localStorage.getItem("token");
    loading.value = true;
    try {
        const res = await fetch(
            `https://0kadddxyh3.execute-api.us-east-1.amazonaws.com/movies/${props.id}`,
            {
                headers: [["Authorization", `Bearer ${token}`]],
            }
        );
        const data = await res.json();
        movie.value = data;
    } catch (err) {
        console.log(err);
        movieError.value = err.message;
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await getMovies();
});
</script>
<template>
    <div class="container mx-auto px-4">
        <div v-if="flash.message">
            {{ flash.message }}
        </div>
        <template v-if="loading">
            <div
                class="flex flex-col gap-5 items-center justify-center w-full h-screen"
            >
                <FilmIcon
                    class="size-10 bg-emerald-400 animate-bounce rounded-full text-white p-2"
                />
                <p class="text-white text-2xl font-bold">loading movie...</p>
            </div>
        </template>
        <template v-else>
            <header class="py-20">
                <nav class="py-10 flex items-center justify-between">
                    <Link class="text-white underline" href="/">Back Home</Link>
                    <Link
                        v-if="auth.user"
                        href="/favorite"
                        as="button"
                        method="post"
                        :data="{
                            movie_id: id,
                            title: movie.title,
                            poster_url: movie.posterUrl,
                        }"
                        class="bg-yellow-500 text-white p-2 rounded-lg font-bold"
                    >
                        add to favorites
                    </Link>
                </nav>
                <h1 class="text-6xl font-bold text-white">
                    {{ movie?.title }}
                </h1>
            </header>
            <main>
                <div class="flex gap-10">
                    <img
                        v-if="movie.posterUrl"
                        class="w-1/3"
                        :src="movie.posterUrl"
                        :alt="movie.title"
                    />
                    <div
                        class="w-2/3 h-96 border-2 border-dashed bg-slate-700 grid place-content-center"
                        v-else
                    >
                        <p class="text-white">No Poster Available</p>
                    </div>
                    <div class="flex flex-col gap-5">
                        <div class="flex items-center gap-3">
                            <span
                                v-for="genre in movie.genres"
                                :key="genre.id"
                                class="bg-emerald-500 text-white rounded-full px-2"
                                >{{ genre.title }}</span
                            >
                        </div>
                        <p class="text-white text-2xl">{{ movie.summary }}</p>
                    </div>
                </div>
            </main>
        </template>
    </div>
</template>

<style scoped></style>
