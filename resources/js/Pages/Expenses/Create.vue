<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    trip: Object,
    participants: Array,
    currencies: Array
});

const form = useForm({
    description: '',
    amount: '',
    currency: props.trip.currency,
    date: new Date().toISOString().split('T')[0],
    paid_by: props.participants[0]?.id,
    split_method: 'equal',
    shares: {}
});

// Initialize shares
props.participants.forEach(participant => {
    form.shares[participant.id] = participant.id === form.paid_by ? 100 : 0;
});

// Calculate shares when method changes
watch(() => form.split_method, (method) => {
    const totalParticipants = props.participants.length;

    props.participants.forEach(participant => {
        if (method === 'equal') {
            form.shares[participant.id] = 100 / totalParticipants;
        } else if (method === 'percentage') {
            form.shares[participant.id] = participant.id === form.paid_by ? 100 : 0;
        } else {
            form.shares[participant.id] = 0;
        }
    });
});

// Calculate total share percentage
const totalShare = computed(() => {
    return Object.values(form.shares).reduce((sum, share) => sum + parseFloat(share || 0), 0);
});

// Format currency
const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
        minimumFractionDigits: 2
    }).format(amount);
};

// Calculate each user's share amount
const shareAmount = (userId) => {
    if (!form.amount || totalShare.value === 0) return 0;
    return (form.shares[userId] / totalShare.value) * form.amount;
};

const validationError = computed(() => {
    if (!form.description || !form.amount || !form.date) {
        return 'Please fill all required fields';
    }

    if (form.split_method === 'percentage' && Math.abs(totalShare.value - 100) > 0.01) {
        return 'Total shares must add up to exactly 100%';
    }

    if (form.split_method === 'custom') {
        const total = Object.values(form.shares).reduce((sum, share) => sum + parseFloat(share || 0), 0);
        if (Math.abs(total - form.amount) > 0.01) {
            return `Total custom amounts must add up to ${formatCurrency(form.amount, form.currency)}`;
        }
    }

    return null;
});
</script>

<template>
    <Head :title="`Add Expense to ${trip.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Add Expense to {{ trip.title }}
                </h2>
                <Link
                    :href="route('trips.expenses.index', trip.id)"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                    Back to Expenses
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden transition-colors duration-300">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div v-if="form.errors.shares" class="mb-4 text-red-600 dark:text-red-400">
                            {{ form.errors.shares }}
                        </div>

                        <div v-if="validationError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded">
                            {{ validationError }}
                        </div>

                        <form @submit.prevent="form.post(route('trips.expenses.store', trip.id))">
                            <!-- Expense Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                    <input
                                        type="text"
                                        v-model="form.description"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                            {{ form.currency }}
                                        </span>
                                        <input
                                            type="number"
                                            v-model="form.amount"
                                            min="0.01"
                                            step="0.01"
                                            class="pl-12 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            required
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                    <input
                                        type="date"
                                        v-model="form.date"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paid By</label>
                                    <select
                                        v-model="form.paid_by"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    >
                                        <option
                                            v-for="participant in participants"
                                            :key="participant.id"
                                            :value="participant.id"
                                        >
                                            {{ participant.name }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Split Method</label>
                                    <select
                                        v-model="form.split_method"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="equal">Equally</option>
                                        <option value="percentage">By Percentage</option>
                                        <option value="custom">Custom Amounts</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Split Details -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4 text-gray-800 dark:text-gray-200">Split Details</h3>
                                <div class="space-y-4">
                                    <div v-for="participant in participants" :key="participant.id" class="flex items-center">
                                        <div class="w-1/3">
                                            <span class="text-gray-900 dark:text-gray-100">{{ participant.name }}</span>
                                            <span v-if="participant.id === form.paid_by" class="ml-2 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                                Paid
                                            </span>
                                        </div>

                                        <div class="w-1/3">
                                            <div v-if="form.split_method === 'equal'" class="text-gray-600 dark:text-gray-400">
                                                {{ (100 / participants.length).toFixed(2) }}%
                                            </div>

                                            <input
                                                v-else
                                                type="number"
                                                v-model="form.shares[participant.id]"
                                                :min="0"
                                                :step="form.split_method === 'percentage' ? 1 : 0.01"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            />
                                        </div>

                                        <div class="w-1/3 text-right font-medium text-gray-900 dark:text-gray-100">
                                            {{ formatCurrency(shareAmount(participant.id), form.currency) }}
                                        </div>
                                    </div>

                                    <div class="flex items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="w-1/3 font-medium text-gray-900 dark:text-gray-100">Total</div>
                                        <div class="w-1/3">
                                            <span v-if="form.split_method === 'equal'">100%</span>
                                            <span v-else class="font-medium" :class="{'text-green-600 dark:text-green-400': totalShare === 100, 'text-red-600 dark:text-red-400': totalShare !== 100}">
                                                {{ totalShare.toFixed(2) }}%
                                            </span>
                                        </div>
                                        <div class="w-1/3 text-right font-medium text-gray-900 dark:text-gray-100">
                                            {{ formatCurrency(form.amount, form.currency) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 disabled:opacity-50 transition-colors"
                                    :disabled="form.processing || !!validationError"
                                >
                                    Add Expense
                                </button>   
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>