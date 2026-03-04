<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-400 hover:to-primary-500 focus:from-primary-400 focus:to-primary-500 active:from-primary-600 active:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-300 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg shadow-primary-500/25">
        <slot />
    </button>
</template>
