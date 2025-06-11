<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from "@/Components/NavLink.vue";

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link :href="route('home')">
                        <ApplicationLogo class="block h-14 w-auto text-gray-800" />
                    </Link>
                    <NavLink :href="route('trips.index')" :active="route().current('trips.index')">
                        My Trips
                    </NavLink>
                    <NavLink :href="route('trips.invites')" :active="route().current('trips.invites')">
                        Invites
                    </NavLink>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden sm:flex sm:items-center sm:gap-6">
                    <template v-if="user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="inline-flex items-center text text-gray-600 hover:text-blue-600">
                                    Hi, {{ user.name }}
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </template>
                    <template v-else>
                        <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-blue-600">Login</Link>
                        <Link href="/register" class="text-sm font-medium text-gray-600 hover:text-blue-600">Register</Link>
                    </template>
                </div>

                <!-- Mobile Button -->
                <div class="sm:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                v-if="!showingNavigationDropdown"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="showingNavigationDropdown" class="sm:hidden px-4 pt-2 pb-4">
            <template v-if="user">
                <Link :href="route('profile.edit')" class="block py-2 text-sm text-gray-600 hover:text-blue-600">Profile</Link>
                <Link :href="route('logout')" method="post" as="button" class="block py-2 text-sm text-red-600 hover:underline">Log Out</Link>
            </template>
            <template v-else>
                <Link href="/login" class="block py-2 text-sm text-gray-600 hover:text-blue-600">Login</Link>
                <Link href="/register" class="block py-2 text-sm text-gray-600 hover:text-blue-600">Register</Link>
            </template>
        </div>
    </nav>
</template>
