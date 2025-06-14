<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from "@/Components/NavLink.vue";
import InviteCounter from "@/Components/InviteCounter.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <Link :href="route('home')">
                        <ApplicationLogo class="block h-14 w-auto text-gray-800 dark:text-gray-200" />
                    </Link>

                    <!-- Navigation Links - Desktop -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-4">
                        <ResponsiveNavLink 
                            :href="route('trips.index')" 
                            :active="route().current('trips.index')"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            :class="{
                                'text-blue-600 dark:text-blue-400 bg-gray-100/50 dark:bg-gray-700/50': route().current('trips.index')
                            }"
                        >
                            My Trips
                        </ResponsiveNavLink>
                        <ResponsiveNavLink 
                            :href="route('trips.invites')" 
                            :active="route().current('trips.invites')" 
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative"
                            :class="{
                                'text-blue-600 dark:text-blue-400 bg-gray-100/50 dark:bg-gray-700/50': route().current('trips.invites')
                            }"
                        >
                            Invites
                            <InviteCounter />
                        </ResponsiveNavLink>
                    </div>
                </div>

                <!-- Desktop Nav (User Section) -->
                <div class="hidden sm:flex sm:items-center sm:gap-6">
                    <template v-if="user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
                        <Link href="/login" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Login
                        </Link>
                        <Link href="/register" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Register
                        </Link>
                    </template>
                </div>

                <!-- Mobile Button -->
                <div class="sm:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors"
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
        <div 
            v-if="showingNavigationDropdown" 
            class="sm:hidden px-4 pt-2 pb-4 space-y-2 bg-white dark:bg-gray-800 transition-colors duration-300"
        >
            <Link
                :href="route('trips.index')"
                class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :class="{
                    'text-blue-600 dark:text-blue-400 bg-gray-100/50 dark:bg-gray-700/50': route().current('trips.index')
                }"
            >
                My Trips
            </Link>
            <Link
                :href="route('trips.invites')"
                class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative"
                :class="{
                    'text-blue-600 dark:text-blue-400 bg-gray-100/50 dark:bg-gray-700/50': route().current('trips.invites')
                }"
            >
                Invites
                <span
                    v-if="user && user.pending_invites_count > 0"
                    class="absolute top-2 right-3 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 dark:bg-red-500 rounded-full"
                >
                    {{ user.pending_invites_count }}
                </span>
            </Link>

            <template v-if="user">
                <Link 
                    :href="route('profile.edit')" 
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    Profile
                </Link>
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button" 
                    class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                    Log Out
                </Link>
            </template>
            <template v-else>
                <Link 
                    href="/login" 
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    Login
                </Link>
                <Link 
                    href="/register" 
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    Register
                </Link>
            </template>
        </div>
    </nav>
</template>