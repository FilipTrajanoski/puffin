<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const count = ref(0);
let channel = null;

onMounted(() => {
    try {
        const user = usePage().props.auth.user;
        if (!user) {
            count.value = 0;
            return;
        }

        count.value = user.pending_invites_count;

        if (!window.Echo) {
            console.error('Echo is not initialized');
            return;
        }

        // Create channel
        channel = window.Echo.private(`trip-invites.${user.id}`);

        // Listen for events
        channel.listen('.TripInviteEvent', (e) => {
            console.log('Received TripInviteEvent:', e);
            count.value = e.count;
        });

        // Error handling
        channel.error((error) => {
            console.error('Channel error:', error);
        });

        // Connection monitoring
        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            console.log('Pusher connection state changed:', states.current);
        });

    } catch (error) {
        console.error('Error in InviteCounter:', error);
    }
});

onUnmounted(() => {
    const user = usePage().props.auth.user;
    if (channel && user) {
        channel.stopListening('.TripInviteEvent');
        window.Echo.leave(`trip-invites.${user.id}`);
    }
});
</script>

<template>
    <span v-if="count > 0"
          class="absolute -top-2 -right-2 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-600 rounded-full">
        {{ count }}
    </span>
</template>
