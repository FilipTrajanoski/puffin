<script setup>
import { ref } from 'vue';
import Navbar from "@/Components/Navbar.vue";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="dark:bg-gray-900 dark:text-gray-100 min-h-screen">
        <Navbar />

        <!-- Page Heading -->
        <header
            class="bg-blue-800 dark:bg-blue-900 shadow"
            v-if="$slots.header"
        >
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header"/>
            </div>
        </header>

        <!-- Page Content -->
        <main class="transition-colors duration-300">
            <slot/>
        </main>
    </div>
</template>