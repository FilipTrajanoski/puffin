<!-- resources/js/Pages/Home.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Pages/Trips/Sidebar.vue'
import TripExpenses from '@/Pages/Trips/TripExpenses.vue'
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Navbar from '@/Components/Navbar.vue';

const trip = {
        id : "1",
        creatorId : "124",
        title : "124",
        currency : "124",
        createdAt : "124",
        updatedAt : "124",
    };
const tripTwo = {
        id : "2",
        creatorId : "224",
        title : "224",
        currency : "224",
        createdAt : "224",
        updatedAt : "224",
    };
let selected = null;

function select(id) {
    this.selected = id;
};

const acceptedTrips = [trip, tripTwo];
</script>

<template>
    <Head title="Home" />
    <HomeLayout>
        <div class="container">
            <Sidebar
                :acceptedTrips="acceptedTrips"
                :selected="selected"
                class="sidebar"
            >
            </Sidebar>
            <TripExpenses 
                :tripId="selected"
                class="tripExpenses"
            />
        </div>
    </HomeLayout>
</template>
<style scoped>
.container {
    display: flex;
    flex-grow: 1;
    justify-content: space-between;
}
.sidebar {
    width: 40dvw;
    margin-top: 60;
}
.tripExpenses {
    width: 60dvw;
    margin-left: 100px;
    margin-top: 60;
}
</style>