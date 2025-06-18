<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, router} from '@inertiajs/vue3';
import {ref, onMounted, onUnmounted} from 'vue';
import {usePage} from '@inertiajs/vue3';
import BackLink from "@/Components/BackLink.vue";

const authUser = usePage().props.auth.user;

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
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <BackLink back-route="trips.index" text="Back to Trips"/>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-2">
                            {{ tripData.title }}
                        </h1>
                    </div>
                    <Link
                        :href="route('trips.expenses.index', trip.id)"
                        class="px-5 py-2.5 flex items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md"
                    >
                        View Expenses
                    </Link>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                    <!-- Form Errors -->
                    <div v-if="Object.keys(participantForm.errors).length"
                         class="p-4 mb-6 text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded">
                        <p v-for="(error, field) in participantForm.errors" :key="field" class="text-sm">
                            <span v-if="field.includes('usernames')">
                                {{ error.replace('The selected', '') }}
                            </span>
                            <span v-else>
                                {{ error }}
                            </span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 dark:bg-blue-900/10 p-5 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</h3>
                            <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{
                                    tripData.currency
                                }}</p>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/10 p-5 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created By</h3>
                            <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{
                                    tripData.creator.name
                                }}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                            Participants
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                ({{
                                    tripData.accepted_participants.length
                                }} accepted, {{ tripData.pending_invites.length }} pending)
                            </span>
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-3">Accepted
                                    Participants</h4>
                                <ul class="space-y-2">
                                    <li v-for="user in tripData.accepted_participants" :key="user.id"
                                        class="flex items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ user.name.charAt(0) }}{{ user.name.split(' ')[1]?.charAt(0) || '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">@{{ user.username }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div v-if="tripData.pending_invites.length">
                                <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-3">Pending
                                    Invitations</h4>
                                <ul class="space-y-2">
                                    <li v-for="user in tripData.pending_invites" :key="user.id"
                                        class="flex items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                        <div
                                            class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                                {{ user.name.charAt(0) }}{{ user.name.split(' ')[1]?.charAt(0) || '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">@{{ user.username }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Invite More Participants Form -->
                    <div v-if="authUser && authUser.id === tripData.creator.id">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                            Invite More Participants
                        </h3>

                        <form @submit.prevent="submitForm">
                            <div v-for="(username, index) in participantForm.usernames" :key="index"
                                 class="flex items-center mb-3">
                                <!-- Display field in view mode when filled -->
                                <div v-if="filledFields[index]" class="flex items-center w-full">
                                    <span
                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-gray-100">
                                        {{ username }}
                                    </span>
                                    <div class="flex ml-3 space-x-3">
                                        <button
                                            type="button"
                                            @click="editUsername(index)"
                                            class="px-4 py-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            @click="removeUsernameField(index)"
                                            class="px-4 py-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
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
                                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    type="button"
                                    @click="addUsernameField"
                                    class="px-4 py-3 text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                >
                                    + Add Another Participant
                                </button>

                                <button
                                    type="submit"
                                    class="ml-auto px-5 py-2.5 text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md"
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
    </AuthenticatedLayout>
</template>
