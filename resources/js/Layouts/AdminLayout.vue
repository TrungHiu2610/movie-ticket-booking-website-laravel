<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-4">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <nav class="flex flex-col space-y-2">
                <Link
                    :href="route('admin.dashboard')"
                    class="px-4 py-2 rounded hover:bg-gray-700"
                    >Dashboard</Link
                >
                <Link href="#" class="px-4 py-2 rounded hover:bg-gray-700"
                    >Quản lý Phim</Link
                >
                <Link href="#" class="px-4 py-2 rounded hover:bg-gray-700"
                    >Quản lý Rạp</Link
                >
                <Link href="#" class="px-4 py-2 rounded hover:bg-gray-700"
                    >Quản lý Lịch chiếu</Link
                >
                <!-- Thêm các link khác ở đây -->
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="mb-6">
                <!-- Header content, e.g., user dropdown -->
            </header>

            <!-- Đây là nơi nội dung của từng trang con sẽ được chèn vào -->
            <slot />
        </main>
    </div>
</template>
