<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    trip: Object
});

// Use an array for usernames
const participantForm = useForm({
    usernames: ['']
});

// Track which fields have been filled
const filledFields = ref([]);

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
</script>

<template>
    <Head :title="trip.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ trip.title }}
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Form Errors -->
                        <div v-if="Object.keys(participantForm.errors).length" class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                            <p v-for="(error, field) in participantForm.errors" :key="field" class="text-sm">
                                <span v-if="field.includes('usernames')">
                                    {{ error.replace('The selected', '') }}
                                </span>
                                <span v-else>
                                    {{ error }}
                                </span>
                            </p>
                        </div>

<!--                        &lt;!&ndash; Success Message &ndash;&gt;-->
<!--                        <div v-if="$page.props.flash.message" class="p-4 mb-4 text-green-700 bg-green-100 rounded">-->
<!--                            {{ $page.props.flash.message }}-->
<!--                        </div>-->

                        <!-- Trip Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium">Trip Information</h3>
                            <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-500">Currency</p>
                                    <p class="font-medium">{{ trip.currency }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Created By</p>
                                    <p class="font-medium">{{ trip.creator.name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Participants Section -->
                        <div class="mb-6">
                            <h3 class="flex items-center text-lg font-medium">
                                Participants
                                <span class="ml-2 text-sm text-gray-500">
                                    ({{ trip.accepted_participants.length }} accepted,
                                    {{ trip.pending_invites.length }} pending)
                                </span>
                            </h3>

                            <div class="mt-4">
                                <h4 class="mb-2 font-medium text-gray-700">Accepted</h4>
                                <ul class="pl-5 mb-4 list-disc">
                                    <li v-for="user in trip.accepted_participants" :key="user.id">
                                        {{ user.name }} (@{{ user.username }})
                                    </li>
                                </ul>

                                <h4 class="mb-2 font-medium text-gray-700" v-if="trip.pending_invites.length">
                                    Pending Invites
                                </h4>
                                <ul class="pl-5 mb-4 list-disc">
                                    <li v-for="user in trip.pending_invites" :key="user.id">
                                        {{ user.name }} (@{{ user.username }})
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Invite More Participants Form -->
                        <div class="pt-4 mt-6 border-t">
                            <form @submit.prevent="submitForm">
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Invite More Participants
                                    </label>

                                    <div v-for="(username, index) in participantForm.usernames" :key="index" class="flex items-center mb-2">
                                        <!-- Display field in view mode when filled -->
                                        <div v-if="filledFields[index]" class="flex items-center w-full">
                                            <span class="px-3 py-2 bg-gray-100 rounded-md">
                                                {{ username }}
                                            </span>
                                            <div class="flex ml-2 space-x-2">
                                                <button
                                                    type="button"
                                                    @click="editUsername(index)"
                                                    class="px-3 py-1 text-blue-600 hover:text-blue-800"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="removeUsernameField(index)"
                                                    class="px-3 py-1 text-red-600 hover:text-red-800"
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
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                                            />
                                        </div>
                                    </div>

                                    <!-- Add Another Field Button -->
                                    <button
                                        type="button"
                                        @click="addUsernameField"
                                        class="px-4 py-2 text-blue-600 border border-blue-600 rounded hover:bg-blue-50"
                                    >
                                        + Add Another Participant
                                    </button>

                                    <!-- Info message -->
                                    <p class="mt-2 text-sm text-gray-500">
                                        Add participants by username
                                    </p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <Link
                                        :href="route('trips.index')"
                                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200"
                                    >
                                        Back to Trips
                                    </Link>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500"
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
