<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    acceptedTrips: Array,
    selected: String
});

const emit = defineEmits(['select']);

const handleClick = (id) => {
    emit('select', id);
};
</script>

<template>
    <div class="sidebar">
        <h2 class="text-xl font-semibold mb-4">My Trips</h2>
        <Link
            :href="route('trips.create')"
            class="px-4 py-2 mb-4 inline-block text-white bg-blue-600 rounded hover:bg-blue-500"
        >
            Create New Trip
        </Link>

        <div v-if="acceptedTrips.length === 0" class="text-center py-8">
            <p class="text-gray-500">You have no trips yet</p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="trip in acceptedTrips"
                :key="trip.id"
                @click="handleClick(trip.id)"
                :class="['trip-item', selected === trip.id ? 'selected' : '']"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ trip.title }}</h3>
                        <p class="text-sm text-gray-500">Currency: {{ trip.currency }}</p>
                        <p class="text-sm text-gray-400">
                            Created: {{ new Date(trip.createdAt).toLocaleDateString() }}
                        </p>
                    </div>
                    <div>
                        <Link
                            :href="route('trips.show', trip.id)"
                            class="text-blue-600 hover:underline text-sm"
                            @click.stop
                        >
                            Edit
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.sidebar {
    width: 40%;
    padding: 1.5rem;
    border-right: 1px solid #e5e7eb;
    background-color: #f9fafb;
    overflow-y: auto;
}

.trip-item {
    cursor: pointer;
    padding: 1rem;
    transition: background 0.2s ease;
}

/* Right side highlight when selected */
.selected {
    border-right: 6px solid #3b82f6; /* Tailwind's blue-500 */
    background-color: #eef6ff;
}
</style>
