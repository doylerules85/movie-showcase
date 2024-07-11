<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import {
    ArrowLeftIcon,
    ArrowRightIcon,
    ExclamationCircleIcon,
    FilmIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import { Link, router, usePage } from "@inertiajs/vue3";

const { auth } = usePage().props;

type Movie = {
    id: string;
    posterUrl?: string;
    title: string;
    rating: string;
};

const token = ref<string>("");
const movies = ref<{ data: Movie[]; totalPages: number }>();
let page = ref<number>(1);
const tokenError = ref<string>("");
const movieError = ref<string>("");
const loading = ref(true);

let searchinput = ref<string>("");
let input = ref<HTMLInputElement>();

const clearInput = () => {
    searchinput.value = "";
    getMovies(token.value);
};

let selectedGenre = ref<string>("");
const genres = [
    "Action",
    "Adventure",
    "Animation",
    "Biography",
    "Comedy",
    "Crime",
    "Drama",
    "Family",
    "Fantasy",
    "Film-Noir",
    "Horror",
    "Music",
    "Musical",
    "Mystery",
    "Romance",
    "Sci-Fi",
    "Short",
    "Sport",
    "Thriller",
    "War",
    "Western",
];

const getToken = async () => {
    if (window.localStorage.getItem("token") !== null) {
        let storedtoken = window.localStorage.getItem("token");
        return (token.value = storedtoken);
    }
    try {
        const res = await fetch(
            "https://0kadddxyh3.execute-api.us-east-1.amazonaws.com/auth/token"
        );
        const data = await res.json();
        token.value = data.token;
        window.localStorage.setItem("token", token.value);
    } catch (err) {
        console.log(err);
        tokenError.value = err;
    }
};

const getMovies = async (token: string) => {
    loading.value = true;
    try {
        const res = await fetch(
            `https://0kadddxyh3.execute-api.us-east-1.amazonaws.com/movies?page=${page.value}&search=${searchinput.value}&genre=${selectedGenre.value}`,
            {
                headers: [["Authorization", `Bearer ${token}`]],
            }
        );
        const data = await res.json();
        movies.value = data;
    } catch (err) {
        console.log(err);
        movieError.value = err.message;
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await getToken();
    await getMovies(token.value);
});

const nextPage = () => {
    if (page.value < movies.value?.totalPages) {
        page.value += 1;
    }
};
const prevPage = () => {
    if (page.value > 1) {
        page.value -= 1;
    }
};

const setPage = (selectedPage: number) => {
    page.value = selectedPage;
};

const searchMovies = () => {
    page.value = 1;
    getMovies(token.value);
    router.get("/", { search: searchinput.value }, { preserveState: true });
};

watch(page, () => {
    getMovies(token.value);
    router.get("/", { page: page.value }, { preserveState: true });
});
watch(selectedGenre, () => {
    getMovies(token.value);
});
</script>

<template>
    <header>
        <nav class="p-4 flex gap-4 justify-end">
            <template v-if="auth.user">
                <Link class="text-white underline" href="/favorites"
                    >My Favorites</Link
                >
                <Link
                    class="text-white underline"
                    href="/logout"
                    method="post"
                    as="button"
                    >Logout</Link
                >
            </template>
            <template v-else>
                <Link class="text-white underline" href="/login"
                    >Login To Save Favorites</Link
                >
            </template>
        </nav>
        <h1
            class="text-3xl font-bold text-white text-center bg-emerald-600 p-3"
        >
            Let's go to the movies!
        </h1>
    </header>
    <main class="container mx-auto py-20 px-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-10">
                <form @submit.prevent="searchMovies">
                    <label class="text-white font-bold" for="search">
                        search movies
                    </label>
                    <div class="flex item-center gap-3">
                        <div class="relative">
                            <input
                                ref="input"
                                id="search"
                                type="text"
                                v-model="searchinput"
                                placeholder="Lord of the Rings"
                                class="rounded-lg placeholder:text-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
                            />
                            <button
                                @click="clearInput"
                                v-if="searchinput.length"
                                class="absolute right-2 top-2"
                            >
                                <XMarkIcon class="size-6" />
                            </button>
                        </div>
                        <button
                            class="bg-emerald-600 text-white p-2 rounded-lg disabled:bg-slate-400 disabled:text-slate-300"
                            :disabled="searchinput === ''"
                        >
                            search
                        </button>
                    </div>
                </form>
                <div class="flex items-end gap-3">
                    <div class="flex flex-col">
                        <label class="text-white font-bold" for="genre"
                            >select genre</label
                        >
                        <select
                            class="rounded-lg focus:ring-emerald-400 focus:border-emerald-400"
                            name="genre"
                            id="genre"
                            v-model="selectedGenre"
                        >
                            <option value="" select>select value</option>
                            <option
                                v-for="genre in genres"
                                :key="genre"
                                :value="genre"
                            >
                                {{ genre }}
                            </option>
                        </select>
                    </div>
                    <button
                        v-if="selectedGenre"
                        @click="selectedGenre = ''"
                        class="bg-emerald-600 text-white p-2 rounded-lg disabled:bg-slate-400 disabled:text-slate-300"
                    >
                        Clear
                    </button>
                </div>
            </div>
            <p v-if="movies?.data?.length" class="text-white">
                showing {{ movies?.data?.length }} movies
            </p>
        </div>
        <template v-if="loading">
            <div
                class="flex flex-col gap-5 items-center justify-center w-full h-screen"
            >
                <FilmIcon
                    class="size-10 bg-emerald-400 animate-bounce rounded-full text-white p-2"
                />
                <p class="text-white text-2xl font-bold">loading movies...</p>
            </div>
        </template>
        <template v-else-if="!movies.data.length">
            <div
                class="flex flex-col gap-5 items-center justify-center w-full py-24"
            >
                <ExclamationCircleIcon class="size-10 text-white" />
                <p class="text-white text-2xl font-bold">No Movies Found!</p>
            </div>
        </template>
        <template v-else>
            <div class="grid grid-cols-4 grid-rows-1 gap-20 py-20">
                <Link
                    :href="`/movie/${movie.id}`"
                    v-for="movie in movies.data"
                    :key="movie.id"
                    :data-index="movie.id"
                    class="flex flex-col gap-5"
                >
                    <img
                        v-if="movie.posterUrl"
                        :src="movie.posterUrl"
                        :alt="`poster image of ${movie.title}`"
                        loading="lazy"
                    />
                    <div
                        v-else
                        class="no-poster border-2 border-dashed bg-slate-700 flex items-center justify-center rounded-lg h-full max-h-[400px]"
                    >
                        <span class="text-slate-50">No Poster Found</span>
                    </div>
                    <div class="flex flex-col gap-4 text-center flex-1">
                        <h2 class="flex-1 text-xl text-white">
                            {{ movie.title }}
                        </h2>
                        <span
                            class="bg-emerald-900 text-slate-100 p-1 rounded-lg text-xs font-bold self-start"
                            v-if="movie.rating"
                        >
                            Rated: {{ movie.rating }}</span
                        >
                        <span
                            class="bg-emerald-900 text-slate-100 p-1 rounded-lg text-xs font-bold self-start"
                            v-else
                            >No Rating Available</span
                        >
                    </div>
                </Link>
            </div>
            <div class="flex gap-4 justify-center py-10">
                <button
                    v-if="movies.totalPages < 1 || page > 1"
                    aria-label="go to the previous page of results"
                    class="bg-emerald-600 p-1 rounded-full text-white"
                    @click="prevPage"
                >
                    <ArrowLeftIcon class="size-6" />
                </button>
                <button
                    v-for="n in movies.totalPages"
                    :key="n"
                    @click="setPage(n)"
                    class="border border-slate-50 text-white w-8 h-8 rounded-full flex items-center justify-center"
                    :class="{ 'bg-emerald-600': page === n }"
                >
                    {{ n }}
                </button>
                <button
                    v-if="movies.totalPages < 1 || page < movies.totalPages"
                    aria-label="go to the next page of results"
                    class="bg-emerald-600 p-1 rounded-full text-white"
                    @click="nextPage"
                >
                    <ArrowRightIcon class="size-6" />
                </button>
            </div>
        </template>
    </main>
</template>

<style scoped>
img {
    object-fit: cover;
    width: 100%;
    height: 100%;
    max-height: 400px;
}
</style>
