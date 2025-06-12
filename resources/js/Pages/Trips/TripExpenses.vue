<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    tripId: String
});

const expenses = ref([
    { id: 1, description: 'Dinner', amount: 40.00 },
    { id: 2, description: 'Taxi', amount: 25.50 },
    { id: 3, description: 'Hotel', amount: 150.00 }
]);

const totalExpenses = computed(() =>
    expenses.value.reduce((sum, e) => sum + e.amount, 0)
);

const addExpense = () => {
    const id = Date.now();
    expenses.value.push({
        id,
        description: `New Expense ${expenses.value.length + 1}`,
        amount: 0.00
    });
};

const editExpense = (expenseId) => {
    const expense = expenses.value.find(e => e.id === expenseId);
    if (expense) {
        const newDescription = prompt('Edit description:', expense.description);
        const newAmount = parseFloat(prompt('Edit amount:', expense.amount));
        if (!isNaN(newAmount) && newDescription !== null) {
            expense.description = newDescription;
            expense.amount = newAmount;
        }
    }
};

const deleteExpense = (expenseId) => {
    if (confirm('Are you sure you want to delete this expense?')) {
        expenses.value = expenses.value.filter(e => e.id !== expenseId);
    }
};
</script>

<template>
    <div class="expenses-container">
        <div class="header">
            <h2 class="text-2xl font-semibold mb-2">Expenses</h2>
            <button @click="addExpense" class="btn-add">+ Add Expense</button>
        </div>

        <ul class="expense-list">
            <li v-for="expense in expenses" :key="expense.id" class="expense-item">
                <div class="expense-info">
                    <p class="description">{{ expense.description }}</p>
                    <p class="amount">${{ expense.amount.toFixed(2) }}</p>
                </div>
                <div class="actions">
                    <button @click="editExpense(expense.id)" class="btn-edit">Edit</button>
                    <button @click="deleteExpense(expense.id)" class="btn-delete">Delete</button>
                </div>
            </li>
        </ul>

        <div class="total">
            Total: ${{ totalExpenses.toFixed(2) }}
        </div>
    </div>
</template>

<style scoped>
.expenses-container {
    background-color: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-add {
    background-color: #3b82f6;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-add:hover {
    background-color: #2563eb;
}

.expense-list {
    margin-top: 1rem;
    list-style: none;
    padding: 0;
}

.expense-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.expense-info {
    flex: 1;
}

.description {
    font-weight: 500;
    margin-bottom: 0.2rem;
}

.amount {
    color: #4b5563;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-edit, .btn-delete {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
}

.btn-edit {
    background-color: #fbbf24;
    color: white;
}

.btn-edit:hover {
    background-color: #f59e0b;
}

.btn-delete {
    background-color: #ef4444;
    color: white;
}

.btn-delete:hover {
    background-color: #dc2626;
}

.total {
    margin-top: 1rem;
    font-weight: bold;
    text-align: right;
    color: #1f2937;
}
</style>
