<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {ref} from 'vue';

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
    <Head title="Create Trip"/>

    <AuthenticatedLayout>
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                        Create New Trip
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Set up a new trip to start tracking shared expenses
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                    <div v-if="Object.keys(form.errors).length"
                         class="p-4 mb-6 text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded">
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
                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Trip
                                Title</label>
                            <input
                                type="text"
                                v-model="form.title"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                                placeholder="Enter trip name"
                            />
                        </div>

                        <div class="mb-6">
                            <label
                                class="block mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                            <select
                                v-model="form.currency"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                                <option value="" disabled selected>Select a currency</option>
                                <option v-for="currency in currencies" :key="currency" :value="currency">
                                    {{ currency }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Invite Participants
                            </label>

                            <div v-for="(username, index) in form.usernames" :key="index"
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
                                        v-model="form.usernames[index]"
                                        placeholder="Enter username"
                                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <!-- Add Another Field Button -->
                            <button
                                type="button"
                                @click="addUsernameField"
                                class="w-full px-4 py-3 text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            >
                                + Add Another Participant
                            </button>

                            <!-- Info message -->
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                You can leave participant fields empty if you don't want to invite anyone right now
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="route('trips.index')"
                                class="px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="px-5 py-2.5 text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md"
                                :disabled="form.processing"
                            >
                                Create Trip
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
