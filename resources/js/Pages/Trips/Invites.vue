<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; // Import router instead of Inertia

defineProps({
    pendingInvites: Array
});

const acceptInvite = (tripId) => {
    router.post(route('trips.accept', tripId)); // Use router.post
};

const declineInvite = (tripId) => {
    router.delete(route('trips.decline', tripId)); // Use router.delete
};
</script>

<template>
    <Head title="Trip Invites"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Trip Invites
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="pendingInvites.length === 0" class="text-center">
                            <p class="text-gray-500">You have no pending trip invitations</p>
                        </div>

                        <ul v-else class="divide-y divide-gray-200">
                            <li v-for="trip in pendingInvites" :key="trip.id" class="py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ trip.title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Invited by: {{ trip.creator.name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Currency: {{ trip.currency }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            @click="acceptInvite(trip.id)"
                                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-500"
                                        >
                                            Accept
                                        </button>
                                        <button
                                            @click="declineInvite(trip.id)"
                                            class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-500"
                                        >
                                            Decline
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
