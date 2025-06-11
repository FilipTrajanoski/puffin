<script setup>
import {ref, computed} from 'vue';
import {Link, usePage} from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Navbar from "@/Components/Navbar.vue";

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);

function isActive(routeName) {
    return route().current(routeName);
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Navbar/>

        <!-- Page Content -->
        <main class="p-6">
            <slot/>
        </main>
    </div>
</template>
