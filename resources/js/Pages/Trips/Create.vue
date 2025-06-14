<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    currencies: Array
});

const form = useForm({
    title: '',
    currency: '',
    usernames: ['']
});

// Track which fields have been filled
const filledFields = ref([]);

// Add a new username input field
const addUsernameField = () => {
    // Mark current fields as filled if they have content
    form.usernames.forEach((username, index) => {
        if (username.trim() !== '' && !filledFields.value[index]) {
            filledFields.value[index] = true;
        }
    });

    // Add a new empty field
    form.usernames.push('');
    filledFields.value.push(false);
};

// Remove a username field
const removeUsernameField = (index) => {
    if (form.usernames.length > 1) {
        form.usernames.splice(index, 1);
        filledFields.value.splice(index, 1);
    } else {
        // If it's the last field, just clear it
        form.usernames[0] = '';
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
    const nonEmptyUsernames = form.usernames.filter(username => username.trim() !== '');

    // Transform the form data to set the cleaned usernames
    form.transform(data => ({
        ...data,
        usernames: nonEmptyUsernames
    })).post(route('trips.store'));
};
</script>

<template>
    <Head title="Create Trip" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Create New Trip
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg transition-colors">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Form Errors -->
                        <div v-if="Object.keys(form.errors).length" class="p-4 mb-4 text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded">
                            <p v-for="(error, field) in form.errors" :key="field" class="text-sm">
                                <span v-if="field.includes('usernames')">
                                    {{ error.replace('The selected', '') }}
                                </span>
                                <span v-else>
                                    {{ error }}
                                </span>
                            </p>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Trip Title</label>
                                <input
                                    type="text"
                                    v-model="form.title"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                                <select
                                    v-model="form.currency"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    required
                                >
                                    <option value="" disabled>Select a currency</option>
                                    <option v-for="currency in currencies" :key="currency" :value="currency">
                                        {{ currency }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Invite Participants
                                </label>

                                <div v-for="(username, index) in form.usernames" :key="index" class="flex items-center mb-2">
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
                                            v-model="form.usernames[index]"
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
                                    You can leave participant fields empty if you don't want to invite anyone right now
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <Link
                                    :href="route('trips.index')"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 transition-colors"
                                    :disabled="form.processing"
                                >
                                    Create Trip
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>