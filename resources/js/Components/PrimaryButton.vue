<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    color: {
        type: String,
        default: 'blue',
    },
});
</script>

<template>
    <button
        :type="type"
        :class="`inline-flex items-center justify-center rounded-md border border-transparent bg-${color}-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-${color}-700 focus:outline-none focus:ring-2 focus:ring-${color}-500 focus:ring-offset-2`"
    >
        <slot />
    </button>
</template>
