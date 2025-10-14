<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head title="Admin Dashboard" />

    <div>
        <h2 class="text-2xl font-semibold">Welcome to the Admin Dashboard!</h2>
        <p class="mt-2 text-gray-600">
            Đây là nơi để quản lý toàn bộ hệ thống.
        </p>
    </div>
</template>
