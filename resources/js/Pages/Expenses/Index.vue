<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import BackLink from "@/Components/BackLink.vue";

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
    <Head :title="`${trip.title} Expenses`"/>

    <AuthenticatedLayout>
        <div class="min-h-[calc(100vh-4rem)] dark:from-gray-900 dark:to-gray-900 py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <BackLink back-route="trips.show" :params="{trip: trip.id}" text="Back to Trip"/>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200 mt-2">
                            {{ trip.title }} Expenses
                        </h1>
                    </div>
                    <Link
                        :href="route('trips.expenses.create', trip.id)"
                        class="px-5 py-2.5 flex items-center bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                             fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Add Expense
                    </Link>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <div
                        class="bg-card dark:bg-card-dark rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 transition-colors">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Expenses</h3>
                        <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            {{
                                formatCurrency(expenses.reduce((sum, e) => sum + parseFloat(e.amount), 0), trip.currency)
                            }}
                        </p>
                    </div>
                    <div
                        class="bg-card dark:bg-card-dark rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 transition-colors">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">You Owe</h3>
                        <p class="text-xl font-semibold text-danger">
                            {{
                                formatCurrency(settlements.filter(s => s.from === $page.props.auth.user.id).reduce((sum, s) => sum + s.amount, 0), trip.currency)
                            }}
                        </p>
                    </div>
                    <div
                        class="bg-card dark:bg-card-dark rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 transition-colors">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">You Are Owed</h3>
                        <p class="text-xl font-semibold text-success">
                            {{
                                formatCurrency(settlements.filter(s => s.to === $page.props.auth.user.id).reduce((sum, s) => sum + s.amount, 0), trip.currency)
                            }}
                        </p>
                    </div>
                </div>

                <!-- Settlements Section -->
                <div
                    class="bg-card dark:bg-card-dark rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700 transition-colors">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Settlements</h3>
                    <div v-if="settlements.length === 0" class="text-gray-500 dark:text-gray-400 py-4 text-center">
                        No settlements needed - all expenses are balanced.
                    </div>
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="settlement in settlements" :key="settlement.from + '-' + settlement.to" class="py-3">
                            <div class="flex items-center">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ participants.find(p => p.id === settlement.from).name }}
                                </span>
                                <span class="mx-2 text-gray-400 dark:text-gray-500">→</span>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ participants.find(p => p.id === settlement.to).name }}
                                </span>
                                <span class="ml-auto font-bold text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(settlement.amount, trip.currency) }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Expenses List -->
                <div
                    class="bg-card dark:bg-card-dark rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Expense History</h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ expenses.length }} expenses
                        </span>
                    </div>

                    <div v-if="expenses.length === 0" class="text-gray-500 dark:text-gray-400 py-8 text-center">
                        No expenses recorded yet.
                    </div>

                    <ul v-else class="space-y-4">
                        <li
                            v-for="expense in expenses"
                            :key="expense.id"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-700"
                        >
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-gray-100">{{
                                            expense.description
                                        }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Paid by <span class="font-medium">{{ expense.payer.name }}</span> •
                                        {{
                                            new Date(expense.date).toLocaleDateString('en-US', {
                                                month: 'short',
                                                day: 'numeric',
                                                year: 'numeric'
                                            })
                                        }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(expense.amount, trip.currency) }}
                                    </p>
                                    <span
                                        class="text-xs bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full">
                                        {{ expense.split_method }} split
                                    </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
