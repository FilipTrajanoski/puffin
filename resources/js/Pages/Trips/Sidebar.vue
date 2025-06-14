<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    acceptedTrips: Array,
    selected: String
});

const emit = defineEmits(['select']);

const handleClick = (id, event) => {
    // Only proceed if the click wasn't on the "View" link
    if (!event.target.closest('a[href]')) {
        emit('select', id);
    }
};
</script>

<template>
    <div class="bg-gray-900 min-h-screen p-6">
        <Link
            :href="route('trips.create')"
            class="px-4 py-2 mb-4 inline-block text-white bg-blue-600 rounded hover:bg-blue-500 transition-colors"
        >
            Create New Trip
        </Link>

        <div v-if="acceptedTrips.length === 0" class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">You have no trips yet</p>
        </div>

        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
            <li
                v-for="trip in acceptedTrips"
                :key="trip.id"
                @click="handleClick(trip.id, $event)"
                :class="[
                    'trip-item',
                    'bg-gray-50 dark:bg-gray-800',
                    'hover:bg-gray-100 dark:hover:bg-gray-700',
                    selected === trip.id ? 'selected' : ''
                ]"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ trip.title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Currency: {{ trip.currency }}</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">
                            Created: {{ new Date(trip.createdAt).toLocaleDateString() }}
                        </p>
                    </div>
                    <div @click.stop>
                        <Link
                            :href="route('trips.show', trip.id)"
                            class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                        >
                            View
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.sidebar {
    width: 70%;
    padding: 1.5rem;
    border-right: 1px solid #e5e7eb;
    background-color: #f9fafb;
    overflow-y: auto;
    margin-left: 15%;
    margin-top: 2%;
    transition: background-color 0.3s ease;
}

.dark .sidebar {
    background-color: #111827;
    border-right-color: #374151;
}

.trip-item {
    cursor: pointer;
    padding: 1rem;
    transition: all 0.2s ease;
    margin: 0.5rem 0;
    border-radius: 0.375rem;
}

/* Right side highlight when selected */
.selected {
    border-right: 4px solid #3b82f6;
    background-color: #eef6ff;
}

.dark .selected {
    background-color: #1e3a8a;
    border-right-color: #60a5fa;
}
</style>