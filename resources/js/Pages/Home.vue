<!-- resources/js/Pages/Home.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import HomeLayout from '@/Layouts/HomeLayout.vue';
</script>

<template>
    <Head title="Home" />

    <HomeLayout>
        <div class="container mx-auto p-8 text-center">
            <h1 class="text-3xl font-bold mb-4">Welcome to Puffin!</h1>
            <p class="mb-4">Track expenses, share them with friends, and settle up with ease.</p>
        </div>
    </HomeLayout>
</template>
