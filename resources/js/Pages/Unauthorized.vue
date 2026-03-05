<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 py-12">
    <div class="max-w-md w-full text-center">
      <!-- Animated Lock Icon -->
      <div class="mb-8">
        <div class="relative inline-block">
          <div class="w-24 h-24 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Error Code -->
      <div class="mb-4">
        <span class="text-6xl font-bold text-gray-200 dark:text-gray-700">403</span>
      </div>

      <!-- Title -->
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
        Access Denied
      </h1>

      <!-- Description -->
      <p class="text-gray-600 dark:text-gray-400 mb-8">
        Sorry, you don't have permission to access this page. 
        <span v-if="userRole" class="block mt-2">
          This page is restricted to <strong class="text-red-500 capitalize">{{ userRole }}</strong> users only.
        </span>
      </p>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <button
          @click="goBack"
          class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Go Back
        </button>

        <Link
          :href="dashboardUrl"
          class="w-full px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Go to Dashboard
        </Link>
      </div>

      <!-- Contact Admin -->
      <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Need help? Contact your administrator for access permissions.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

const userRole = computed(() => {
  if (props.user && props.user.role) {
    return props.user.role.slug;
  }
  return null;
});

const dashboardUrl = computed(() => {
  return '/dashboard';
});

const goBack = () => {
  window.history.back();
};
</script>
