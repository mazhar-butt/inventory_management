<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
    stats: Object,
});

const isAdmin = computed(() => {
    return props.stats?.isAdmin === true || (props.user?.role?.slug === 'admin');
});

const isManager = computed(() => {
    return props.stats?.isManager === true || (props.user?.role?.slug === 'manager');
});

const roleBgColor = computed(() => {
    if (!props.user?.role) return 'bg-emerald-100 text-emerald-700';
    const slug = props.user.role.slug;
    if (slug === 'admin') return 'bg-red-100 text-red-700';
    if (slug === 'manager') return 'bg-amber-100 text-amber-700';
    return 'bg-emerald-100 text-emerald-700';
});

const getCurrentGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good Morning';
    if (hour < 18) return 'Good Afternoon';
    return 'Good Evening';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PK', {
        style: 'currency',
        currency: 'PKR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

// User permissions
const userPermissions = computed(() => {
    return props.user?.role?.permissions || [];
});

// Admin stat cards
const adminStatCards = computed(() => [
    {
        title: "Today's Sale",
        value: formatCurrency(props.stats?.todaySale || 0),
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'emerald',
        gradient: 'from-emerald-500 to-emerald-600',
        bgGradient: 'bg-gradient-to-br from-emerald-50 to-white',
        borderColor: 'border-emerald-200',
        subtitle: 'Today',
        iconBg: 'bg-emerald-100',
        iconColor: 'text-emerald-600',
    },
    {
        title: 'Monthly Sale',
        value: formatCurrency(props.stats?.monthlySale || 0),
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        color: 'blue',
        gradient: 'from-blue-500 to-blue-600',
        bgGradient: 'bg-gradient-to-br from-blue-50 to-white',
        borderColor: 'border-blue-200',
        subtitle: 'This Month',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
    },
    {
        title: 'Total Orders',
        value: props.stats?.totalOrders || 0,
        icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        color: 'purple',
        gradient: 'from-purple-500 to-purple-600',
        bgGradient: 'bg-gradient-to-br from-purple-50 to-white',
        borderColor: 'border-purple-200',
        subtitle: 'All Time',
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
    },
    {
        title: 'Low Stock Items',
        value: props.stats?.lowStockItems?.length || 0,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        color: 'red',
        gradient: 'from-red-500 to-red-600',
        bgGradient: 'bg-gradient-to-br from-red-50 to-white',
        borderColor: 'border-red-200',
        subtitle: 'Below 10 units',
        iconBg: 'bg-red-100',
        iconColor: 'text-red-600',
    },
]);

// Manager stat cards
const managerStatCards = computed(() => [
    {
        title: "Today's Sale",
        value: formatCurrency(props.stats?.todaySale || 0),
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'emerald',
        gradient: 'from-emerald-500 to-emerald-600',
        bgGradient: 'bg-gradient-to-br from-emerald-50 to-white',
        borderColor: 'border-emerald-200',
        subtitle: 'Your Branch',
        iconBg: 'bg-emerald-100',
        iconColor: 'text-emerald-600',
    },
    {
        title: 'Monthly Sale',
        value: formatCurrency(props.stats?.monthlySale || 0),
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        color: 'blue',
        gradient: 'from-blue-500 to-blue-600',
        bgGradient: 'bg-gradient-to-br from-blue-50 to-white',
        borderColor: 'border-blue-200',
        subtitle: 'Your Branch',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600',
    },
    {
        title: 'Total Orders',
        value: props.stats?.totalOrders || 0,
        icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        color: 'purple',
        gradient: 'from-purple-500 to-purple-600',
        bgGradient: 'bg-gradient-to-br from-purple-50 to-white',
        borderColor: 'border-purple-200',
        subtitle: 'Your Branch',
        iconBg: 'bg-purple-100',
        iconColor: 'text-purple-600',
    },
    {
        title: 'Low Stock Items',
        value: props.stats?.lowStockItems?.length || 0,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        color: 'orange',
        gradient: 'from-orange-500 to-orange-600',
        bgGradient: 'bg-gradient-to-br from-orange-50 to-white',
        borderColor: 'border-orange-200',
        subtitle: 'Your Branch',
        iconBg: 'bg-orange-100',
        iconColor: 'text-orange-600',
    },
]);

// Regular stat cards
const regularStatCards = computed(() => [
    {
        title: 'Total Branches',
        value: props.stats?.totalBranches || 0,
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        color: 'blue',
        trend: '+12%',
        trendUp: true,
    },
    {
        title: 'Active Branches',
        value: props.stats?.activeBranches || 0,
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'emerald',
        trend: '+8%',
        trendUp: true,
    },
    {
        title: 'Total Products',
        value: props.stats?.totalProducts || 0,
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        color: 'purple',
        trend: '+24%',
        trendUp: true,
    },
    {
        title: 'Your Role',
        value: props.user?.role?.name || 'N/A',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        color: 'orange',
        isRole: true,
    },
]);

const statCards = computed(() => {
    if (isAdmin.value) return adminStatCards.value;
    if (isManager.value) return managerStatCards.value;
    return regularStatCards.value;
});

const getLowStockSeverity = (quantity) => {
    if (quantity <= 0) return { color: 'text-red-600', label: 'Out of Stock', bg: 'bg-red-100' };
    if (quantity <= 5) return { color: 'text-red-500', label: 'Critical', bg: 'bg-red-100' };
    return { color: 'text-amber-500', label: 'Low', bg: 'bg-amber-100' };
};

// Calculate max sales for line graph scaling
const maxSales = computed(() => {
    if (!props.stats?.salesTrend) return 100;
    const max = Math.max(...props.stats.salesTrend.map(s => s.total_sales));
    return max > 0 ? max : 100;
});

// Get Y-axis position for line graph
const getYPosition = (sales) => {
    if (maxSales.value === 0) return 100;
    const percentage = (sales / maxSales.value) * 100;
    return 100 - percentage;
};

// Generate line path for SVG
const linePath = computed(() => {
    if (!props.stats?.salesTrend || props.stats.salesTrend.length === 0) return '';
    
    const width = 100;
    const height = 100;
    const stepX = width / (props.stats.salesTrend.length - 1);
    
    const points = props.stats.salesTrend.map((item, index) => {
        const x = index * stepX;
        const y = getYPosition(item.total_sales);
        return `${x},${y}`;
    });
    
    return `M ${points.join(' L ')}`;
});

// Get dot positions for line graph
const dotPositions = computed(() => {
    if (!props.stats?.salesTrend) return [];
    
    const width = 100;
    const height = 100;
    const stepX = width / (props.stats.salesTrend.length - 1);
    
    return props.stats.salesTrend.map((item, index) => ({
        x: index * stepX,
        y: getYPosition(item.total_sales),
        sales: item.total_sales,
        products: item.products
    }));
});
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- Enhanced Welcome Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-700 to-slate-800 rounded-3xl p-8 mb-8 text-white shadow-2xl">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute top-1/2 left-1/2 w-32 h-32 bg-emerald-400/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            
            <div class="relative flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-slate-300 text-sm font-medium mb-2">{{ getCurrentGreeting() }},</p>
                    <h1 class="text-4xl font-bold mb-3">{{ user?.name || 'Guest' }}!</h1>
                    <p class="text-slate-300 text-lg max-w-xl">
                        <span v-if="isAdmin">Here's your business overview. Monitor sales, orders, inventory, and branch performance at a glance.</span>
                        <span v-else-if="isManager">Here's your branch overview. Monitor sales, orders, and inventory for your branch.</span>
                        <span v-else>Here's your business overview. Monitor your branches and products at a glance.</span>
                    </p>
                    
                    <!-- Quick Stats Row -->
                    <div class="flex flex-wrap gap-6 mt-6">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                            <span class="text-slate-300 text-sm">All systems operational</span>
                        </div>
                        <div v-if="!isAdmin && !isManager" class="flex items-center gap-2">
                            <span class="text-slate-300 text-sm">{{ user.role?.name || 'User' }}</span>
                        </div>
                        <div v-if="isAdmin" class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                            <span class="text-slate-300 text-sm">Admin Dashboard</span>
                        </div>
                        <div v-if="isManager" class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                            <span class="text-slate-300 text-sm">Manager Dashboard - {{ user?.branch?.name || 'Branch' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- User Avatar -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="w-32 h-32 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center shadow-2xl transform rotate-3 hover:rotate-6 transition-transform duration-300">
                            <span class="text-5xl font-bold text-white">{{ user?.name?.charAt(0) || 'G' }}</span>
                        </div>
                        <div v-if="isAdmin" class="absolute -bottom-2 -right-2 w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center shadow-lg border-4 border-slate-800">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div v-else-if="isManager" class="absolute -bottom-2 -right-2 w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg border-4 border-slate-800">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div v-else class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg border-4 border-slate-800">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Dashboard Content -->
        <template v-if="isAdmin">
            <!-- Admin Stats Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div 
                    v-for="(card, index) in statCards" 
                    :key="card.title"
                    class="group relative overflow-hidden rounded-2xl p-6 shadow-sm border transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                    :class="[card.bgGradient, card.borderColor]"
                >
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r" :class="card.gradient"></div>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ card.title }}</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ card.value }}</p>
                            <div v-if="card.subtitle" class="mt-3">
                                <span class="text-xs text-slate-400">{{ card.subtitle }}</span>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110" :class="[card.iconBg]">
                            <svg class="w-7 h-7" :class="card.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Trend Line Graph & Branch Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Sales Trend Line Graph -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-indigo-500 to-indigo-600 rounded-full"></div>
                            Sales Trend (Last 7 Days)
                        </h3>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-full">
                            Line Graph
                        </span>
                    </div>
                    <div class="p-6">
                        <div v-if="stats?.salesTrend?.length > 0" class="relative">
                            <!-- Y-axis labels -->
                            <div class="absolute left-0 top-0 bottom-8 w-8 flex flex-col justify-between text-xs text-slate-400">
                                <span>{{ formatCurrency(maxSales) }}</span>
                                <span>{{ formatCurrency(maxSales / 2) }}</span>
                                <span>0</span>
                            </div>
                            
                            <!-- Line Graph SVG -->
                            <div class="ml-8 relative">
                                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-48">
                                    <!-- Grid lines -->
                                    <line x1="0" y1="25" x2="100" y2="25" stroke="#e2e8f0" stroke-width="0.5" />
                                    <line x1="0" y1="50" x2="100" y2="50" stroke="#e2e8f0" stroke-width="0.5" />
                                    <line x1="0" y1="75" x2="100" y2="75" stroke="#e2e8f0" stroke-width="0.5" />
                                    
                                    <!-- Line path -->
                                    <path :d="linePath" fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    
                                    <!-- Area fill -->
                                    <path :d="`${linePath} L 100,100 L 0,100 Z`" fill="url(#gradient)" opacity="0.2" />
                                    
                                    <!-- Gradient definition -->
                                    <defs>
                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" stop-color="#6366f1" />
                                            <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                    
                                    <!-- Dots -->
                                    <circle v-for="(dot, index) in dotPositions" :key="index" :cx="dot.x" :cy="dot.y" r="2" fill="#6366f1" />
                                </svg>
                                
                                <!-- X-axis labels -->
                                <div class="flex justify-between mt-2">
                                    <span v-for="(day, index) in stats.salesTrend" :key="index" class="text-xs text-slate-500 text-center flex-1">
                                        {{ day.day }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center h-48 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No sales data yet</p>
                        </div>
                    </div>
                    
                    <!-- Product Details Below Graph -->
                    <div v-if="stats?.salesTrend?.length > 0" class="px-6 pb-6">
                        <div class="border-t border-slate-100 pt-4">
                            <h4 class="text-sm font-semibold text-slate-700 mb-3">Products Sold Each Day</h4>
                            <div class="grid grid-cols-1 gap-3">
                                <div v-for="(day, index) in stats.salesTrend" :key="index" class="bg-slate-50 rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-slate-600">{{ day.day }} - {{ day.date }}</span>
                                        <span class="text-sm font-bold text-indigo-600">{{ formatCurrency(day.total_sales) }}</span>
                                    </div>
                                    <div v-if="day.products && day.products.length > 0" class="flex flex-wrap gap-2">
                                        <span v-for="(product, pIndex) in day.products" :key="pIndex" class="inline-flex items-center px-2 py-1 bg-white border border-slate-200 rounded text-xs">
                                            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-1.5"></span>
                                            {{ product.name }}: <span class="font-semibold ml-1">{{ product.quantity }}</span>
                                        </span>
                                    </div>
                                    <div v-else class="text-xs text-slate-400">No products sold</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                            Branch Details
                        </h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                            {{ stats?.branchDetails?.length || 0 }} branches
                        </span>
                    </div>
                    <div class="p-6">
                        <div v-if="stats?.branchDetails?.length > 0" class="space-y-4">
                            <div v-for="branch in stats.branchDetails" :key="branch.id" class="flex items-center justify-between p-4 bg-gradient-to-r from-slate-50 to-white rounded-xl hover:from-slate-100 hover:to-slate-50 transition-colors border border-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="branch.is_active ? 'bg-gradient-to-br from-emerald-400 to-emerald-500' : 'bg-slate-200'">
                                        <svg v-if="branch.is_active" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ branch.name }}</p>
                                        <p class="text-sm text-slate-500">{{ branch.address || 'No address' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-slate-800">{{ formatCurrency(branch.total_sales) }}</p>
                                    <p class="text-sm text-slate-500">{{ branch.orders_count }} orders</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No branches found</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 5 Selling Products -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-white">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                        Top 5 Selling Products
                    </h3>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">
                        All Branches
                    </span>
                </div>
                <div class="p-6">
                    <div v-if="stats?.topSellingProducts?.length > 0" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div v-for="(product, index) in stats.topSellingProducts" :key="product.id" class="relative p-4 rounded-xl border border-slate-100 hover:border-emerald-200 hover:shadow-md transition-all bg-gradient-to-b from-white to-slate-50">
                            <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg" :class="index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-500' : index === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400' : index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700' : 'bg-gradient-to-br from-emerald-500 to-emerald-600'">
                                {{ index + 1 }}
                            </div>
                            <div class="mt-2">
                                <p class="font-semibold text-slate-800 text-sm truncate">{{ product.name }}</p>
                                <p class="text-xs text-slate-500 mb-3">{{ product.sku }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-600">{{ product.total_sold }} sold</span>
                                    <span class="text-xs font-bold text-emerald-600">{{ formatCurrency(product.total_revenue) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">No sales data yet</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items Alert -->
            <div v-if="stats?.lowStockItems?.length > 0" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-red-50 to-white">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-1 h-6 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                        Low Stock Alert
                    </h3>
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">
                        {{ stats.lowStockItems.length }} items need attention
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="item in stats.lowStockItems" :key="item.id" class="p-4 rounded-xl border border-slate-200 hover:border-red-200 hover:shadow-md transition-all">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ item.product?.name || 'Unknown Product' }}</p>
                                    <p class="text-sm text-slate-500">{{ item.product?.sku || 'N/A' }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-lg text-xs font-medium" :class="[getLowStockSeverity(item.quantity).bg, getLowStockSeverity(item.quantity).color]">
                                    {{ getLowStockSeverity(item.quantity).label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-sm text-slate-500">{{ item.branch?.name || 'Unknown Branch' }}</span>
                                </div>
                                <p class="font-bold text-slate-800">{{ item.quantity }} units</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Manager Dashboard Content -->
        <template v-else-if="isManager">
            <!-- Manager Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="(card, index) in statCards" :key="card.title" class="group relative overflow-hidden rounded-2xl p-6 shadow-sm border transition-all duration-300 hover:shadow-xl hover:-translate-y-1" :class="[card.bgGradient, card.borderColor]">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r" :class="card.gradient"></div>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ card.title }}</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ card.value }}</p>
                            <div v-if="card.subtitle" class="mt-3">
                                <span class="text-xs text-slate-400">{{ card.subtitle }}</span>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110" :class="[card.iconBg]">
                            <svg class="w-7 h-7" :class="card.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Trend Line Graph & Branch Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Sales Trend Line Graph -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-amber-500 to-amber-600 rounded-full"></div>
                            Sales Trend (Last 7 Days)
                        </h3>
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">
                            Your Branch
                        </span>
                    </div>
                    <div class="p-6">
                        <div v-if="stats?.salesTrend?.length > 0" class="relative">
                            <div class="absolute left-0 top-0 bottom-8 w-8 flex flex-col justify-between text-xs text-slate-400">
                                <span>{{ formatCurrency(maxSales) }}</span>
                                <span>{{ formatCurrency(maxSales / 2) }}</span>
                                <span>0</span>
                            </div>
                            <div class="ml-8 relative">
                                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-48">
                                    <line x1="0" y1="25" x2="100" y2="25" stroke="#e2e8f0" stroke-width="0.5" />
                                    <line x1="0" y1="50" x2="100" y2="50" stroke="#e2e8f0" stroke-width="0.5" />
                                    <line x1="0" y1="75" x2="100" y2="75" stroke="#e2e8f0" stroke-width="0.5" />
                                    <path :d="linePath" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle v-for="(dot, index) in dotPositions" :key="index" :cx="dot.x" :cy="dot.y" r="2" fill="#f59e0b" />
                                </svg>
                                <div class="flex justify-between mt-2">
                                    <span v-for="(day, index) in stats.salesTrend" :key="index" class="text-xs text-slate-500 text-center flex-1">
                                        {{ day.day }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center h-48 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No sales data yet</p>
                        </div>
                    </div>
                    
                    <!-- Product Details Below Graph -->
                    <div v-if="stats?.salesTrend?.length > 0" class="px-6 pb-6">
                        <div class="border-t border-slate-100 pt-4">
                            <h4 class="text-sm font-semibold text-slate-700 mb-3">Products Sold Each Day</h4>
                            <div class="grid grid-cols-1 gap-3">
                                <div v-for="(day, index) in stats.salesTrend" :key="index" class="bg-slate-50 rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-slate-600">{{ day.day }} - {{ day.date }}</span>
                                        <span class="text-sm font-bold text-amber-600">{{ formatCurrency(day.total_sales) }}</span>
                                    </div>
                                    <div v-if="day.products && day.products.length > 0" class="flex flex-wrap gap-2">
                                        <span v-for="(product, pIndex) in day.products" :key="pIndex" class="inline-flex items-center px-2 py-1 bg-white border border-slate-200 rounded text-xs">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>
                                            {{ product.name }}: <span class="font-semibold ml-1">{{ product.quantity }}</span>
                                        </span>
                                    </div>
                                    <div v-else class="text-xs text-slate-400">No products sold</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                            Your Branch
                        </h3>
                        <span v-if="stats?.myBranch?.is_active" class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">
                            Active
                        </span>
                        <span v-else class="px-3 py-1 bg-slate-100 text-slate-700 text-sm font-medium rounded-full">
                            Inactive
                        </span>
                    </div>
                    <div class="p-6">
                        <div v-if="stats?.myBranch" class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-slate-50 to-white rounded-xl border border-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-400 to-blue-500">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ stats.myBranch.name }}</p>
                                        <p class="text-sm text-slate-500">{{ stats.myBranch.address || 'No address' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-emerald-50 rounded-xl text-center">
                                    <p class="text-2xl font-bold text-emerald-600">{{ formatCurrency(stats.myBranch.orders?.sum('grand_total') || 0) }}</p>
                                    <p class="text-sm text-emerald-600">Total Sales</p>
                                </div>
                                <div class="p-4 bg-blue-50 rounded-xl text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ stats.myBranch.orders?.count() || 0 }}</p>
                                    <p class="text-sm text-blue-600">Total Orders</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No branch assigned</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products for Manager -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-white">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                        Top Selling Products
                    </h3>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">
                        Your Branch
                    </span>
                </div>
                <div class="p-6">
                    <div v-if="stats?.topSellingProducts?.length > 0" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div v-for="(product, index) in stats.topSellingProducts" :key="product.id" class="relative p-4 rounded-xl border border-slate-100 hover:border-emerald-200 hover:shadow-md transition-all bg-gradient-to-b from-white to-slate-50">
                            <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg" :class="index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-500' : index === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400' : index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700' : 'bg-gradient-to-br from-emerald-500 to-emerald-600'">
                                {{ index + 1 }}
                            </div>
                            <div class="mt-2">
                                <p class="font-semibold text-slate-800 text-sm truncate">{{ product.name }}</p>
                                <p class="text-xs text-slate-500 mb-3">{{ product.sku }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-600">{{ product.total_sold }} sold</span>
                                    <span class="text-xs font-bold text-emerald-600">{{ formatCurrency(product.total_revenue) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">No sales data yet</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert for Manager -->
            <div v-if="stats?.lowStockItems?.length > 0" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50 to-white">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <div class="w-1 h-6 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
                        Low Stock Alert
                    </h3>
                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded-full">
                        {{ stats.lowStockItems.length }} items need attention
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="item in stats.lowStockItems" :key="item.id" class="p-4 rounded-xl border border-slate-200 hover:border-orange-200 hover:shadow-md transition-all">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ item.product?.name || 'Unknown Product' }}</p>
                                    <p class="text-sm text-slate-500">{{ item.product?.sku || 'N/A' }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-lg text-xs font-medium" :class="[getLowStockSeverity(item.quantity).bg, getLowStockSeverity(item.quantity).color]">
                                    {{ getLowStockSeverity(item.quantity).label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                                <span class="text-sm text-slate-500">Available Stock</span>
                                <p class="font-bold text-slate-800">{{ item.quantity }} units</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Non-Admin/Non-Manager Dashboard Content -->
        <template v-else>
            <!-- Regular Stats Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="(card, index) in statCards" :key="card.title" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:border-slate-200 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ card.title }}</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ card.isRole ? card.value : card.value }}</p>
                            <div v-if="!card.isRole" class="mt-3 flex items-center gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="card.trendUp ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.trendUp ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'"></path>
                                    </svg>
                                    {{ card.trend }}
                                </span>
                                <span class="text-xs text-slate-400">vs last month</span>
                            </div>
                            <div v-else class="mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="roleBgColor">
                                    {{ user?.role?.slug || 'No Role' }}
                                </span>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110" :class="card.color === 'blue' ? 'bg-blue-50' : card.color === 'emerald' ? 'bg-emerald-50' : card.color === 'purple' ? 'bg-purple-50' : 'bg-orange-50'">
                            <svg class="w-7 h-7" :class="card.color === 'blue' ? 'text-blue-500' : card.color === 'emerald' ? 'text-emerald-500' : card.color === 'purple' ? 'text-purple-500' : 'text-orange-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Permissions Card -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                            Your Permissions
                        </h3>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">
                            {{ userPermissions.length }} total
                        </span>
                    </div>
                    <div class="p-6">
                        <div v-if="userPermissions.length > 0" class="flex flex-wrap gap-2">
                            <span v-for="permission in userPermissions" :key="permission.id" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all duration-200 cursor-default">
                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ permission.slug.replace('.', ' - ') }}
                            </span>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No permissions assigned</p>
                        </div>
                    </div>
                </div>

                <!-- Role Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                            <div class="w-1 h-6 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                            Role Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div v-if="user?.role" class="space-y-4">
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 rounded-2xl flex items-center justify-center bg-purple-50">
                                    <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Role Name</span>
                                    <span class="font-semibold text-slate-800">{{ user.role.name }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Role Slug</span>
                                    <code class="bg-slate-200 px-3 py-1 rounded-lg text-sm font-mono text-slate-700">{{ user.role.slug }}</code>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-slate-500">Description</span>
                                    <span class="text-slate-800 text-right text-sm">{{ user.role.description || 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">No role assigned</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
                    Quick Actions
                </h3>
                <span class="text-sm text-slate-400">Navigate faster</span>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    <Link v-if="userPermissions.some(p => p.slug === 'branches.index')" :href="route('admin.branches.index')" class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5">
                        <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </span>
                        View Branches
                    </Link>
                    <Link v-if="userPermissions.some(p => p.slug === 'branches.create')" :href="route('admin.branches.create')" class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5">
                        <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </span>
                        Add Branch
                    </Link>
                    <Link v-if="isAdmin" :href="route('admin.products.index')" class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl font-medium hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-0.5">
                        <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </span>
                        View Products
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 flex items-center justify-between text-sm text-slate-400">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span>Last updated: Just now</span>
            </div>
            <div class="flex items-center gap-4">
                <span>Dashboard v2.0</span>
                <span class="text-slate-300">|</span>
                <span>Built with Laravel + Jetstream</span>
            </div>
        </div>
    </AppLayout>
</template>
