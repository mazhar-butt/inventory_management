<template>
    <Teleport to="body">
        <div v-if="show" :class="['loader-overlay', { dark: darkTheme }]">
            <div class="loader-container">
                <!-- Outer rotating ring -->
                <div class="loader-ring-outer"></div>
                
                <!-- Middle counter-rotating ring -->
                <div class="loader-ring-middle"></div>
                
                <!-- Inner pulsing ring -->
                <div class="loader-ring-inner"></div>
                
                <!-- Center glowing dot -->
                <div class="loader-center"></div>
                
                <!-- Loading text -->
                <span class="loader-text">{{ message }}</span>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

// Props for customization
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    message: {
        type: String,
        default: 'Loading...',
    },
    darkTheme: {
        type: Boolean,
        default: false,
    },
});
</script>
