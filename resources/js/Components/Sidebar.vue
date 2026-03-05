<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    routes: {
        type: Array,
        required: true,
    },
});

const page = usePage();

// Get user role from auth data - properly extract the role slug
const userRole = computed(() => {
    // The role is passed as a string (slug) from HandleInertiaRequests middleware
    const role = page.props.auth?.user?.role;
    if (typeof role === 'string') {
        return role;
    }
    if (role && typeof role === 'object' && role.slug) {
        return role.slug;
    }
    return role || 'guest';
});

// Show menu items based on user role
const navItems = computed(() => {
    const role = userRole.value;
    
    if (role === 'admin') {
        return [
            { name: 'dashboard', label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
            { name: 'products', label: 'Products', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10V4m0 0L12 4m8 4l8-4m-8 4l-8-4' },
            { name: 'branches', label: 'Branches', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
            { name: 'inventories', label: 'Inventories', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
            { name: 'movements', label: 'Movement', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
        ];
    } else if (role === 'manager') {
        return [
            { name: 'dashboard', label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
            { name: 'orders', label: 'Orders', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
            { name: 'inventories', label: 'Inventories', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
            { name: 'movements', label: 'Movement', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
        ];
    } else if (role === 'sales') {
        return [
            { name: 'dashboard', label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
            { name: 'orders', label: 'Orders', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
        ];
    }
    
    // Default (guest or unknown role)
    return [
        { name: 'dashboard', label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    ];
});

const isActive = (routeName) => {
    const url = getRouteUrl(routeName);
    return window.location.href.includes(url);
};

// Get the correct URL prefix based on user role
const getBaseUrl = () => {
    if (userRole.value === 'admin') return '/admin';
    if (userRole.value === 'manager') return '/manager';
    if (userRole.value === 'sales') return '/sales';
    return '/admin';
};

const getRouteUrl = (routeName) => {
    const baseUrl = getBaseUrl();
    const routeMap = {
        'dashboard': baseUrl + '/dashboard',
        'products': baseUrl + '/products',
        'branches': baseUrl + '/branches',
        'inventories': baseUrl + '/inventories',
        'orders': baseUrl + '/orders',
        'movements': baseUrl + '/movements',
    };
    return routeMap[routeName] || '#';
};
</script>

<template>
    <aside class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col shadow-2xl z-50">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-700/50 bg-slate-900/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-blinds-icon lucide-blinds"><path d="M3 3h18"/><path d="M20 7H8"/><path d="M20 11H8"/><path d="M10 19h10"/><path d="M8 15h12"/><path d="M4 3v14"/><circle cx="4" cy="19" r="2"/></svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-white tracking-tight">Inventory</span>
                    <p class="text-xs text-slate-400 -mt-0.5">Management</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 scrollbar-hide">
            <div class="space-y-1">
                <div class="px-3 mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Main Menu</span>
                </div>
                
                <a
                    v-for="(item, index) in navItems"
                    :key="item.name"
                    :href="getRouteUrl(item.name)"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="isActive(item.name) 
                        ? 'bg-gradient-to-r from-[#E67823] to-orange-600 text-white shadow-lg shadow-orange-600/30' 
                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'"
                >
                    <div 
                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="isActive(item.name) 
                            ? 'bg-white/20' 
                            : 'bg-slate-700/50 group-hover:bg-[#E67823]/20'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="isActive(item.name) ? 'text-white' : 'text-slate-400 group-hover:text-[#E67823]'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ item.label }}</span>
                    
                    <!-- Active indicator -->
                    <div 
                        v-if="isActive(item.name)"
                        class="ml-auto w-2 h-2 rounded-full bg-white shadow-sm"
                    ></div>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-700/50 bg-slate-900/50">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 hover:bg-slate-800 transition-colors cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center text-white font-bold shadow-lg">
                    {{ page.props.auth?.user?.name?.charAt(0) || 'U' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ page.props.auth?.user?.name || 'User' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ page.props.auth?.user?.role_name || 'guest' }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
            </div>
        </div>
    </aside>
</template>
