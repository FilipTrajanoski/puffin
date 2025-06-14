<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Pages/Trips/Sidebar.vue'
import HomeLayout from '@/Layouts/HomeLayout.vue';

function select(id) {
  this.selected = id;
};
</script>

<template>
  <Head title="Home" />
  <HomeLayout>
    <div class="container bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    </div>
  </HomeLayout>
</template>

<style scoped>
.container {
  display: flex;
  flex-grow: 1;
  justify-content: space-between;
  min-height: calc(100vh - 4rem);
}

.sidebar {
  width: 80dvw;
  margin-top: 60px;
  background: #111827;
  border-right-color: #374151;
}

.dark .sidebar {
  background: #111827;
  border-right-color: #374151;
}

.tripExpenses {
  width: 60dvw;
  margin-left: 100px;
  margin-top: 60px;
  background: white;
}

.dark .tripExpenses {
  background: #111827;
}

@media (max-width: 768px) {
  .container {
    flex-direction: column;
  }
  
  .sidebar,
  .tripExpenses {
    width: 100%;
    margin-left: 0;
  }
  
  .tripExpenses {
    margin-top: 20px;
  }
}
</style>