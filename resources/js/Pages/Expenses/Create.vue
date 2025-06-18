<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {computed, ref, watch} from 'vue';
import BackLink from "@/Components/BackLink.vue";

const props = defineProps({
    trip: Object,
    participants: Array
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

// Initialize shares properly
const initializeShares = () => {
    if (form.split_method === 'equal') {
        const sharePerPerson = 100 / props.participants.length;
        props.participants.forEach(participant => {
            form.shares[participant.id] = sharePerPerson;
        });
    } else if (form.split_method === 'percentage') {
        props.participants.forEach(participant => {
            form.shares[participant.id] = participant.id === form.paid_by ? 100 : 0;
        });
    } else { // custom
        props.participants.forEach(participant => {
            form.shares[participant.id] = 0;
        });
    }
};

// Initialize on component creation
initializeShares();

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

const totalShare = computed(() => {
    if (form.split_method === 'custom') {
        return Object.values(form.shares).reduce(
            (sum, share) => sum + parseFloat(share || 0),
            0
        );
    } else {
        return Object.values(form.shares).reduce(
            (sum, share) => sum + parseFloat(share || 0),
            0
        );
    }
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
    if (!form.amount) return 0;

    if (form.split_method === 'equal') {
        return form.amount / props.participants.length;
    } else if (form.split_method === 'percentage') {
        return (form.shares[userId] / 100) * form.amount;
    } else { // custom
        return form.shares[userId] || 0;
    }
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
    <Head :title="`Add Expense to ${trip.title}`"/>

    <AuthenticatedLayout>
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <BackLink back-route="trips.expenses.index" :params="trip.id" text="Back to Expenses"/>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-2">
                            Add Expense to {{ trip.title }}
                        </h1>
                    </div>
                </div>

                <div
                    class="bg-card dark:bg-card-dark rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div v-if="form.errors.shares"
                         class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded">
                        {{ form.errors.shares }}
                    </div>

                    <div v-if="validationError"
                         class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded">
                        {{ validationError }}
                    </div>

                    <form @submit.prevent="form.post(route('trips.expenses.store', trip.id))">
                        <!-- Expense Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <input
                                    type="text"
                                    v-model="form.description"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                        {{ form.currency }}
                                    </span>
                                    <input
                                        type="number"
                                        v-model="form.amount"
                                        min="0.01"
                                        step="0.01"
                                        class="pl-12 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input
                                    type="date"
                                    v-model="form.date"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Paid
                                    By</label>
                                <select
                                    v-model="form.paid_by"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Split
                                    Method</label>
                                <select
                                    v-model="form.split_method"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                                <div v-for="participant in participants" :key="participant.id"
                                     class="flex items-center">
                                    <div class="w-1/3">
                                        <span class="text-gray-900 dark:text-gray-100">{{ participant.name }}</span>
                                        <span v-if="participant.id === form.paid_by"
                                              class="ml-2 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                            Paid
                                        </span>
                                    </div>

                                    <div class="w-1/3">
                                        <div v-if="form.split_method === 'equal'"
                                             class="text-gray-600 dark:text-gray-400">
                                            {{ (100 / participants.length).toFixed(2) }}%
                                        </div>

                                        <input
                                            v-else
                                            type="number"
                                            v-model="form.shares[participant.id]"
                                            :min="0"
                                            :step="form.split_method === 'percentage' ? 1 : 0.01"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                                        <span v-else-if="form.split_method === 'percentage'" class="font-medium"
                                              :class="{'text-success': Math.abs(totalShare - 100) < 0.01,
                                                        'text-danger': Math.abs(totalShare - 100) >= 0.01
                                                    }">
                                            {{ totalShare.toFixed(2) }}%
                                        </span>
                                        <span v-else class="font-medium" :class="{
                                            'text-success': Math.abs(totalShare - form.amount) < 0.01,
                                            'text-danger': Math.abs(totalShare - form.amount) >= 0.01
                                        }">
                                            {{ formatCurrency(totalShare, form.currency) }}
                                        </span>
                                    </div>
                                    <div class="w-1/3 text-right font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(form.amount, form.currency) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button
                                type="submit"
                                class="px-5 py-2.5 text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md"
                                :disabled="form.processing || !!validationError"
                            >
                                Add Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
