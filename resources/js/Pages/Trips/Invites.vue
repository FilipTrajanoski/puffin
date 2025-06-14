<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import {onMounted, onUnmounted, ref} from "vue";

const props = defineProps({
    pendingInvites: Array
});

const invites = ref([...props.pendingInvites]);

onMounted(() => {
    const userId = usePage().props.auth.user.id;

    window.Echo.private(`trip-invites.${userId}`)
        .listen('.TripInviteEvent', (e) => {
            // Refresh the invites list
            router.reload({ only: ['pendingInvites'] });
        });
});

onUnmounted(() => {
    const userId = usePage().props.auth.user.id;
    window.Echo.leave(`trip-invites.${userId}`);
});

const acceptInvite = (tripId) => {
    router.post(route('trips.accept', tripId));
};

const declineInvite = (tripId) => {
    router.delete(route('trips.decline', tripId));
};
</script>

<template>
    <Head title="Trip Invites"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Trip Invites
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg transition-colors">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div v-if="pendingInvites.length === 0" class="text-center">
                            <p class="text-gray-500 dark:text-gray-400">You have no pending trip invitations</p>
                        </div>

                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="trip in pendingInvites" :key="trip.id" class="py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ trip.title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Invited by: {{ trip.creator.name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Currency: {{ trip.currency }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            @click="acceptInvite(trip.id)"
                                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-500 transition-colors"
                                        >
                                            Accept
                                        </button>
                                        <button
                                            @click="declineInvite(trip.id)"
                                            class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-500 transition-colors"
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