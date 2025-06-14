<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    trip: Object
});

// Use an array for usernames
const participantForm = useForm({
    usernames: ['']
});

// Track which fields have been filled
const filledFields = ref([]);

// Add real-time trip updates
const tripData = ref({...props.trip});

// Add a new username input field
const addUsernameField = () => {
    // Mark current fields as filled if they have content
    participantForm.usernames.forEach((username, index) => {
        if (username.trim() !== '' && !filledFields.value[index]) {
            filledFields.value[index] = true;
        }
    });

    // Add a new empty field
    participantForm.usernames.push('');
    filledFields.value.push(false);
};
    
// Remove a username field
const removeUsernameField = (index) => {
    if (participantForm.usernames.length > 1) {
        participantForm.usernames.splice(index, 1);
        filledFields.value.splice(index, 1);
    } else {
        // If it's the last field, just clear it
        participantForm.usernames[0] = '';
        filledFields.value[0] = false;
    }
};

// Enable editing of a filled field
const editUsername = (index) => {
    filledFields.value[index] = false;
};

// Handle form submission with better error handling
const submitForm = () => {
    // Filter out empty usernames
    const nonEmptyUsernames = participantForm.usernames.filter(username => username.trim() !== '');

    // Transform the form data to set the cleaned usernames
    participantForm.transform(data => ({
        ...data,
        usernames: nonEmptyUsernames
    })).put(route('trips.update', props.trip.id));
};

// Listen for trip updates
onMounted(() => {
    window.Echo.private(`trip-updates.${props.trip.id}`)
        .listen('.TripUpdatedEvent', (e) => {
            console.log('Received TripUpdatedEvent, refreshing trip data...');
            // Refresh trip data
            router.reload({
                only: ['trip'],
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    tripData.value = {...props.trip};
                }
            });
        });
});

onUnmounted(() => {
    window.Echo.leave(`trip-updates.${props.trip.id}`);
});
</script>

<template>
    <Head :title="tripData.title"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <Link 
                    :href="route('trips.index')"
                    class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                >
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ tripData.title }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg transition-colors">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Form Errors -->
                        <div v-if="Object.keys(participantForm.errors).length"
                             class="p-4 mb-4 text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded">
                            <p v-for="(error, field) in participantForm.errors" :key="field" class="text-sm">
                                <span v-if="field.includes('usernames')">
                                    {{ error.replace('The selected', '') }}
                                </span>
                                <span v-else>
                                    {{ error }}
                                </span>
                            </p>
                        </div>

                        <!-- Trip Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Trip Information</h3>
                            <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Currency</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ tripData.currency }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ tripData.creator.name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Participants Section -->
                        <div class="mb-6">
                            <h3 class="flex items-center text-lg font-medium text-gray-900 dark:text-gray-100">
                                Participants
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    ({{ tripData.accepted_participants.length }} accepted,
                                    {{ tripData.pending_invites.length }} pending)
                                </span>
                            </h3>

                            <div class="mt-4">
                                <h4 class="mb-2 font-medium text-gray-700 dark:text-gray-300">Accepted</h4>
                                <ul class="pl-5 mb-4 list-disc text-gray-900 dark:text-gray-100">
                                    <li v-for="user in tripData.accepted_participants" :key="user.id">
                                        {{ user.name }} (@{{ user.username }})
                                    </li>
                                </ul>

                                <h4 class="mb-2 font-medium text-gray-700 dark:text-gray-300" v-if="tripData.pending_invites.length">
                                    Pending Invites
                                </h4>
                                <ul class="pl-5 mb-4 list-disc text-gray-900 dark:text-gray-100">
                                    <li v-for="user in tripData.pending_invites" :key="user.id">
                                        {{ user.name }} (@{{ user.username }})
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Invite More Participants Form -->
                        <div class="pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                            <form @submit.prevent="submitForm">
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Invite More Participants
                                    </label>

                                    <div v-for="(username, index) in participantForm.usernames" :key="index"
                                         class="flex items-center mb-2">
                                        <!-- Display field in view mode when filled -->
                                        <div v-if="filledFields[index]" class="flex items-center w-full">
                                            <span class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-gray-100">
                                                {{ username }}
                                            </span>
                                            <div class="flex ml-2 space-x-2">
                                                <button
                                                    type="button"
                                                    @click="editUsername(index)"
                                                    class="px-3 py-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="removeUsernameField(index)"
                                                    class="px-3 py-1 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Display field in edit mode when not filled -->
                                        <div v-else class="flex items-center w-full">
                                            <input
                                                type="text"
                                                v-model="participantForm.usernames[index]"
                                                placeholder="Enter username"
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            />
                                        </div>
                                    </div>

                                    <!-- Add Another Field Button -->
                                    <button
                                        type="button"
                                        @click="addUsernameField"
                                        class="px-4 py-2 text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                    >
                                        + Add Another Participant
                                    </button>

                                    <!-- Info message -->
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Add participants by username
                                    </p>
                                </div>

                                <div class="pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                                    <Link
                                        :href="route('trips.expenses.index', trip.id)"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition-colors"
                                    >
                                        View Expenses
                                    </Link> 
                                </div>

                                <div class="flex items-center justify-end">  <!-- Changed from justify-between to justify-end -->
                                    <button
                                        type="submit"
                                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 transition-colors"
                                        :disabled="participantForm.processing"
                                    >
                                        Send Invites
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>