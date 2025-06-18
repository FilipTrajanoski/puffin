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
            router.reload({only: ['pendingInvites']});
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
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                        Trip Invitations
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Manage your pending trip invitations
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                    <div v-if="pendingInvites.length === 0" class="text-center py-8">
                        <div
                            class="mx-auto w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 dark:text-blue-400"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">No pending
                            invitations</h3>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                            You don't have any pending trip invitations at the moment
                        </p>
                    </div>

                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="trip in pendingInvites" :key="trip.id" class="py-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        {{ trip.title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span class="mr-3">Invited by:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{
                                                trip.creator.name
                                            }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ trip.currency }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex space-x-3 mt-3 sm:mt-0">
                                    <button
                                        @click="acceptInvite(trip.id)"
                                        class="px-5 py-2.5 flex items-center bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md"
                                    >
                                        Accept
                                    </button>
                                    <button
                                        @click="declineInvite(trip.id)"
                                        class="px-5 py-2.5 flex items-center bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg hover:from-red-600 hover:to-rose-700 transition-all shadow-md"
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
    </AuthenticatedLayout>
</template>
