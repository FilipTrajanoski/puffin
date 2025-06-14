<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    trip: Object,
    expenses: Array,
    settlements: Array,
    participants: Array
});

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
        minimumFractionDigits: 2
    }).format(amount);
};
</script>

<template>
    <Head :title="`${trip.title} Expenses`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ trip.title }} Expenses
                </h2>
                <Link
                    :href="route('trips.expenses.create', trip.id)"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 transition-colors"
                >
                    Add Expense
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow transition-colors">
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Expenses</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ formatCurrency(expenses.reduce((sum, e) => sum + parseFloat(e.amount), 0), trip.currency) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow transition-colors">
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">You Owe</h3>
                        <p class="text-2xl font-semibold text-red-600 dark:text-red-400">
                            {{ formatCurrency(settlements.filter(s => s.from === $page.props.auth.user.id).reduce((sum, s) => sum + s.amount, 0), trip.currency) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow transition-colors">
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">You Are Owed</h3>
                        <p class="text-2xl font-semibold text-green-600 dark:text-green-400">
                            {{ formatCurrency(settlements.filter(s => s.to === $page.props.auth.user.id).reduce((sum, s) => sum + s.amount, 0), trip.currency) }}
                        </p>
                    </div>
                </div>

                <!-- Settlements Section -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6 overflow-hidden transition-colors">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Settlements</h3>
                        <div v-if="settlements.length === 0" class="text-gray-500 dark:text-gray-400">
                            No settlements needed - all expenses are balanced.
                        </div>
                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="settlement in settlements" :key="settlement.from + '-' + settlement.to" class="py-3">
                                <div class="flex items-center">
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ participants.find(p => p.id === settlement.from).name }}
                                    </span>
                                    <span class="mx-2 text-gray-400 dark:text-gray-500">→</span>
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ participants.find(p => p.id === settlement.to).name }}
                                    </span>
                                    <span class="ml-auto font-medium text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(settlement.amount, trip.currency) }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Expenses List -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden transition-colors">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Expense History</h3>
                        <div v-if="expenses.length === 0" class="text-gray-500 dark:text-gray-400">
                            No expenses recorded yet.
                        </div>
                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="expense in expenses" :key="expense.id" class="py-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ expense.description }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Paid by {{ expense.payer.name }} on {{ new Date(expense.date).toLocaleDateString() }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ formatCurrency(expense.amount, expense.currency) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ expense.split_method }} split
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>