<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    acceptedTrips: Array,
    selected: String
});

const emit = defineEmits(['select']);

const handleClick = (id, event) => {
    if (!event.target.closest('a[href]')) {
        emit('select', id);
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Your Trips" />
    <AuthenticatedLayout>
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                        Your Travel Expenses
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Manage all your trips and track shared expenses in one place
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                Your Trip Collection
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ acceptedTrips.length }} trips in total
                            </p>
                        </div>
                        <Link
                            :href="route('trips.create')"
                            class="px-5 py-2.5 flex items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md transform hover:scale-[1.02]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Create New Trip
                        </Link>
                    </div>
                </div>

                <div v-if="acceptedTrips.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="mx-auto w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">No trips yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
                        Get started by creating your first trip to track expenses with friends
                    </p>
                </div>

                <ul v-else class="space-y-5">
                    <li
                        v-for="trip in acceptedTrips"
                        :key="trip.id"
                        @click="handleClick(trip.id, $event)"
                        :class="[
                            'trip-item',
                            'bg-white dark:bg-gray-800',
                            'rounded-xl shadow-sm',
                            'border border-gray-200 dark:border-gray-700',
                            'transition-all duration-300',
                            selected === trip.id ? 'selected border-2 border-blue-500 shadow-md' : ''
                        ]"
                    >
                        <Link :href="route('trips.show', trip.id)" class="block h-full p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ trip.title }}</h3>
                                        <span class="ml-3 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ trip.currency }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Created {{ formatDate(trip.created_at) }}
                                    </p>
                                </div>
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.trip-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.trip-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    border-color: rgba(99, 102, 241, 0.3);
}

.selected {
    background-color: rgba(59, 130, 246, 0.05);
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.dark .selected {
    background-color: rgba(30, 58, 138, 0.2);
    border-color: #3b82f6;
}
</style>
