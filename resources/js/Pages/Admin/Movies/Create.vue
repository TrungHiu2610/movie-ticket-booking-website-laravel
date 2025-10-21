<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    genres: Array,
    actors: Array,
    directors: Array,
});

const form = useForm({
    title: "",
    description: "",
    poster_url: null,
    trailer_url: "",
    duration_minutes: 120,
    release_date: "",
    age_rating: "P",
    status: "coming_soon",
    genres: [],
    actors: [],
    directors: [],
});

const submit = () => {
    form.post(route("movies.store"), {
        forceFormData: true,
        onError: (errors) => {
            console.log("Validation errors:", errors);
        },
    });
};
</script>

<template>
    <Head title="Thêm Phim Mới" />

    <h1 class="text-3xl font-bold">Thêm Phim Mới</h1>

    <div class="mt-6 max-w-2xl mx-auto bg-white shadow-md rounded-lg p-8">
        <form @submit.prevent="submit">
            <!-- Title -->
            <div class="mb-4">
                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700"
                    >Tiêu đề</label
                >
                <input
                    v-model="form.title"
                    type="text"
                    id="title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <div v-if="form.errors.title" class="text-sm text-red-600 mt-1">
                    {{ form.errors.title }}
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                    >Mô tả</label
                >
                <textarea
                    v-model="form.description"
                    id="description"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>
                <div
                    v-if="form.errors.description"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.description }}
                </div>
            </div>

            <!-- Poster là 1 file -->
            <div class="mb-4">
                <label
                    for="poster_url"
                    class="block text-sm font-medium text-gray-700"
                    >Poster</label
                >
                <input
                    @change="(e) => (form.poster_url = e.target.files[0])"
                    @input="(e) => (form.poster_url = e.target.files[0])"
                    type="file"
                    id="poster_url"
                    accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                <div
                    v-if="form.errors.poster_url"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.poster_url }}
                </div>
            </div>

            <!-- Trailer URL -->
            <div class="mb-4"></div>
            <label
                for="trailer_url"
                class="block text-sm font-medium text-gray-700"
                >Trailer URL</label
            >
            <input
                v-model="form.trailer_url"
                type="text"
                id="trailer_url"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
            <div
                v-if="form.errors.trailer_url"
                class="text-sm text-red-600 mt-1"
            >
                {{ form.errors.trailer_url }}
            </div>

            <!-- Duration -->
            <div class="mb-4">
                <label
                    for="duration_minutes"
                    class="block text-sm font-medium text-gray-700"
                    >Thời lượng (phút)</label
                >
                <input
                    v-model="form.duration_minutes"
                    type="number"
                    id="duration_minutes"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <div
                    v-if="form.errors.duration_minutes"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.duration_minutes }}
                </div>
            </div>
            <!-- Release Date -->
            <div class="mb-4">
                <label
                    for="release_date"
                    class="block text-sm font-medium text-gray-700"
                    >Ngày phát hành</label
                >
                <input
                    v-model="form.release_date"
                    type="date"
                    id="release_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <div
                    v-if="form.errors.release_date"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.release_date }}
                </div>
            </div>
            <!-- Age Rating -->
            <div class="mb-4">
                <label
                    for="age_rating"
                    class="block text-sm font-medium text-gray-700"
                    >Độ tuổi</label
                >
                <select
                    v-model="form.age_rating"
                    id="age_rating"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="P">P</option>
                    <option value="C13">C13</option>
                    <option value="C16">C16</option>
                    <option value="C18">C18</option>
                </select>
                <div
                    v-if="form.errors.age_rating"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.age_rating }}
                </div>
            </div>
            <!-- Status -->
            <div class="mb-4">
                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700"
                    >Trạng thái</label
                >
                <select
                    v-model="form.status"
                    id="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="coming_soon">Sắp chiếu</option>
                    <option value="now_showing">Đang chiếu</option>
                </select>
                <div
                    v-if="form.errors.status"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.status }}
                </div>
            </div>

            <!-- Genres -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Thể loại</label
                >
                <div
                    class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2"
                >
                    <div
                        v-for="genre in props.genres"
                        :key="genre.id"
                        class="flex items-center"
                    >
                        <input
                            v-model="form.genres"
                            :value="genre.id"
                            type="checkbox"
                            :id="`genre-${genre.id}`"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <label
                            :for="`genre-${genre.id}`"
                            class="ml-2 text-sm text-gray-600"
                            >{{ genre.name }}</label
                        >
                    </div>
                </div>
                <div
                    v-if="form.errors.genres"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.genres }}
                </div>
            </div>

            <!-- Actors -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Diễn viên</label
                >
                <div
                    class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2"
                >
                    <div
                        v-for="actor in props.actors"
                        :key="actor.id"
                        class="flex items-center"
                    >
                        <input
                            v-model="form.actors"
                            :value="actor.id"
                            type="checkbox"
                            :id="`actor-${actor.id}`"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <label
                            :for="`actor-${actor.id}`"
                            class="ml-2 text-sm text-gray-600"
                            >{{ actor.name }}</label
                        >
                    </div>
                </div>
                <div
                    v-if="form.errors.actors"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.actors }}
                </div>
            </div>

            <!-- Directors -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Đạo diễn</label
                >
                <div
                    class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2"
                >
                    <div
                        v-for="director in props.directors"
                        :key="director.id"
                        class="flex items-center"
                    >
                        <input
                            v-model="form.directors"
                            :value="director.id"
                            type="checkbox"
                            :id="`director-${director.id}`"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <label
                            :for="`director-${director.id}`"
                            class="ml-2 text-sm text-gray-600"
                            >{{ director.name }}</label
                        >
                    </div>
                </div>
                <div
                    v-if="form.errors.directors"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ form.errors.directors }}
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-blue-300"
                >
                    Lưu Phim
                </button>
            </div>
        </form>
    </div>
</template>
