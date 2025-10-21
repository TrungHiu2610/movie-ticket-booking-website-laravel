<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
// import Pagination from "@/Components/Pagination.vue"; // Sẽ tạo component này sau

defineOptions({ layout: AdminLayout });

defineProps({
    movies: Object,
});
</script>

<template>
    <Head title="Quản lý Phim" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Danh sách Phim</h1>
        <Link
            :href="route('movies.create')"
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
        >
            Thêm Phim Mới
        </Link>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Poster
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Tiêu đề
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Thời lượng
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Trạng thái
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Thể loại
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Đạo diễn
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Diễn viên
                    </th>
                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="movie in movies.data" :key="movie.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img
                            :src="`/storage/${movie.poster_url}`"
                            :alt="movie.title"
                            class="w-16 h-24 object-cover rounded"
                        />
                    </td>
                    <td
                        class="px-6 py-4 whitespace-nowrap font-medium text-gray-900"
                    >
                        {{ movie.title }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        {{ movie.duration_minutes }} phút
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="{
                                'bg-green-100 text-green-800':
                                    movie.status === 'now_showing',
                                'bg-yellow-100 text-yellow-800':
                                    movie.status === 'coming_soon',
                                'bg-red-100 text-red-800':
                                    movie.status === 'ended',
                            }"
                        >
                            {{ movie.status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        <ul>
                            <li
                                v-for="genre in movie.genres"
                                :key="genre.id"
                                class="inline-block mr-2"
                            >
                                {{ genre.name }}
                            </li>
                        </ul>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        <ul>
                            <li
                                v-for="director in movie.directors"
                                :key="director.id"
                                class="inline-block mr-2"
                            >
                                {{ director.name }}
                            </li>
                        </ul>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        <ul>
                            <li
                                v-for="actor in movie.actors"
                                :key="actor.id"
                                class="inline-block mr-2"
                            >
                                {{ actor.name }}
                            </li>
                        </ul>
                    </td>
                    <td
                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                    >
                        <Link
                            href="#"
                            class="text-indigo-600 hover:text-indigo-900"
                            >Sửa</Link
                        >
                        <Link
                            href="#"
                            class="ml-4 text-red-600 hover:text-red-900"
                            >Xóa</Link
                        >
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Component phân trang sẽ hiển thị ở đây -->
    <!-- <Pagination :links="movies.links" class="mt-6" /> -->
</template>
