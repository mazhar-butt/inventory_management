<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post('/logout');
};

const navRoutes = [
    { name: 'dashboard', label: 'Dashboard', icon: 'IconDashboard' },
    { name: 'branches.index', label: 'Branches', icon: 'IconBranch' },
    { name: 'products.index', label: 'Products', icon: 'IconProduct' },
    { name: 'inventories.index', label: 'Inventories', icon: 'IconInventory' },
    { name: 'orders.index', label: 'Orders', icon: 'IconOrder' },
    { name: 'order-items.index', label: 'Order Items', icon: 'IconOrderItem' },
    { name: 'inventory-movements.index', label: 'Movements', icon: 'IconMovement' },
];
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="flex min-h-screen bg-slate-50">
            <!-- Sidebar -->
            <Sidebar :routes="navRoutes" />

            <!-- Main Content -->
            <div class="flex-1 ml-64 overflow-x-hidden">
                <!-- Top Navigation -->
                <nav class="bg-white shadow-sm border-b border-slate-200">
                    <div class="max-w-7xl mx-auto px-6 py-4">
                        <!-- Custom Header Slot -->
                        <slot name="header"></slot>
                        <!-- Default Header if no slot provided -->
                        <div v-if="!$slots.header" class="flex justify-between items-center">
                            <div class="flex items-center">
                                <h2 class="text-xl font-bold text-slate-800">{{ title }}</h2>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $page.props.auth?.user?.name || 'User' }}</span>
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <div v-if="showingNavigationDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                                        <Link href="/profile" class="flex items-center gap-3 px-4 py-2 text-slate-600 hover:bg-slate-50 hover:text-[#E67823] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profile
                                        </Link>
                                        <hr class="my-2 border-slate-200">
                                        <button @click="logout" class="flex items-center gap-3 w-full px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Log Out
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="py-6 overflow-y-auto h-screen">
                    <div class="max-w-7xl mx-auto px-6">
                        <slot></slot>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
