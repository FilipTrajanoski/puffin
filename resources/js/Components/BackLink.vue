<script setup>
import {Link} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    backRoute: {
        type: String,
        required: true
    },
    params: {
        type: Object,
        default: () => ({})
    }
})

const href = computed(() => route(props.backRoute, props.params))

</script>

<template>
    <Link
        :href="href"
        class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
    >
        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </Link>
</template>

<style scoped>

</style>
