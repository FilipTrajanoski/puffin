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
    },
    text: {
        type: String,
        default: 'Back'
    }
})

const href = computed(() => route(props.backRoute, props.params))

</script>

<template>
    <Link
        :href="href"
        class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                  d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                  clip-rule="evenodd"/>
        </svg>
        {{ text }}
    </Link>
</template>
