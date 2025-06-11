<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    acceptedTrips: Array
});
</script>

<template>
    <Head title="My Trips" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Trips
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <Link
                            :href="route('trips.create')"
                            class="px-4 py-2 mb-4 text-white bg-blue-600 rounded hover:bg-blue-500"
                        >
                            Create New Trip
                        </Link>

                        <div v-if="acceptedTrips.length === 0" class="text-center py-8">
                            <p class="text-gray-500">You have no trips yet</p>
<!--                            <p class="mt-2">-->
<!--                                <Link :href="route('trips.create')" class="text-blue-600 hover:underline">-->
<!--                                    Create your first trip-->
<!--                                </Link>-->
<!--                            </p>-->
                        </div>

                        <ul v-else class="divide-y divide-gray-200">
                            <li v-for="trip in acceptedTrips" :key="trip.id" class="py-4">
                                <Link :href="route('trips.show', trip.id)" class="block">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ trip.title }}</h3>
                                            <p class="text-sm text-gray-500">
                                                Currency: {{ trip.currency }}
                                            </p>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Created: {{ new Date(trip.created_at).toLocaleDateString() }}
                                        </div>
                                    </div>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
