<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import Swal, { showSuccess, showDeleteConfirm } from '@/Plugins/sweetalert';

const props = defineProps({
    orders: Object,
    branches: Array,
    branchInventories: Object,
});

const page = usePage();

// Get the URL prefix based on user role
const getUrlPrefix = () => {
    const role = page.props.auth?.user?.role;
    if (role === 'admin') return '/admin';
    if (role === 'manager') return '/manager';
    if (role === 'sales') return '/sales';
    return '/admin'; // default
};

const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    branch_id: '',
    status: 'completed',
    items: [{ product_id: '', quantity: 1, price: 0 }],
});

const getAvailableProducts = computed(() => {
    if (!form.branch_id || !props.branchInventories) return [];
    return props.branchInventories[form.branch_id] || [];
});

const getAvailableQuantity = (productId) => {
    const product = getAvailableProducts.value.find(p => p.product_id == productId);
    return product ? Number(product.quantity) || 0 : 0;
};

const productOptions = computed(() => {
    return getAvailableProducts.value.map(p => ({ 
        value: p.product_id, 
        label: `${p.name} (${p.sku}) - Stock: ${p.quantity} - ${p.price}` 
    })) || []
});

const calculateTotals = () => {
    let total = 0;
    form.items.forEach(item => {
        const product = getAvailableProducts.value.find(p => p.product_id == item.product_id);
        if (product && item.quantity) {
            const subtotal = product.price * item.quantity;
            const tax = subtotal * (product.tax / 100);
            total += subtotal + tax;
        }
    });
    return total;
};

const grandTotal = computed(() => calculateTotals());

const addItem = () => {
    form.items.push({ product_id: '', quantity: 1, price: 0 });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const getProductPrice = (productId) => {
    const product = getAvailableProducts.value.find(p => p.product_id == productId);
    return product ? Number(product.price) || 0 : 0;
};

// Auto-populate price when product is selected
const onProductSelect = (item) => {
    const product = getAvailableProducts.value.find(p => p.product_id == item.product_id);
    if (product) {
        item.price = product.price;
        // Reset quantity to 1 if it exceeds available stock
        if (item.quantity > product.quantity) {
            item.quantity = 1;
        }
    }
};

const getProductTax = (productId) => {
    const product = getAvailableProducts.value.find(p => p.product_id == productId);
    return product ? Number(product.tax) || 0 : 0;
};

const columns = [
    { key: 'id', label: 'ID', class: 'w-16' },
    { key: 'branch', label: 'Branch' },
    { key: 'user', label: 'Customer' },
    { key: 'total_amount', label: 'Total' },
    { key: 'tax_amount', label: 'Tax' },
    { key: 'grand_total', label: 'Grand Total' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Date' },
];

const branchOptions = computed(() => 
    props.branches?.map(b => ({ value: b.id, label: b.name })) || []
);

const statusOptions = [
    { value: 'completed', label: 'Completed' },
];

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.branch_id = '';
    form.status = 'completed';
    form.items = [{ product_id: '', quantity: 1, price: 0 }];
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.branch_id = item.branch_id;
    form.status = item.status;
    form.items = item.items?.map(i => ({
        product_id: i.product_id,
        quantity: i.quantity,
        price: i.price
    })) || [];
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingItem.value = null;
};

const calculateGrandTotal = () => {
    const total = parseFloat(form.total_amount) || 0;
    const taxPercent = parseFloat(form.tax_percentage) || 0;
    const tax = total * (taxPercent / 100);
    form.tax_amount = tax.toFixed(2);
    form.grand_total = (total + tax).toFixed(2);
};

const handleSubmit = () => {
    if (editingItem.value) {
        const prefix = getUrlPrefix();
        form.put(`${prefix}/orders/${editingItem.value.id}`, {
            onSuccess: () => {
                showSuccess('Order updated successfully');
                closeModal();
            },
        });
    } else {
        const prefix = getUrlPrefix();
        form.post(`${prefix}/orders`, {
            onSuccess: () => {
                showSuccess('Order created successfully');
                closeModal();
            },
        });
    }
};

const handleDelete = (item) => {
    showDeleteConfirm(`Order #${item.id}`).then((result) => {
        if (result.isConfirmed) {
            const prefix = getUrlPrefix();
            form.delete(`${prefix}/orders/${item.id}`, {
                onSuccess: () => {
                    showSuccess('Order deleted successfully');
                },
            });
        }
    });
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-700',
        processing: 'bg-blue-100 text-blue-700',
        completed: 'bg-emerald-100 text-emerald-700',
        cancelled: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-slate-100 text-slate-700';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getTotalRevenue = computed(() => {
    return props.orders?.data?.reduce((sum, item) => sum + (parseFloat(item.grand_total) || 0), 0) || 0;
});

const getPendingOrders = computed(() => {
    return props.orders?.data?.filter(item => item.status === 'pending').length || 0;
});

const getCompletedOrders = computed(() => {
    return props.orders?.data?.filter(item => item.status === 'completed').length || 0;
});
</script>

<template>
    <AppLayout title="Orders">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Order Management
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Track and manage customer orders</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-gradient-to-r from-[#E67823] to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="font-semibold">Create Order</span>
                </button>
            </div>
        </template>

        <div class="py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Orders</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ orders.total || 0 }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#E67823]/10 to-orange-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#E67823]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Revenue</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ formatCurrency(getTotalRevenue) }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pending Orders</p>
                            <p class="text-3xl font-bold text-amber-600 mt-2">{{ getPendingOrders }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Completed</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ getCompletedOrders }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                            <tr>
                                <th v-for="column in columns" :key="column.key" class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">
                                    {{ column.label }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            <tr v-if="orders.data.length === 0">
                                <td :colspan="columns.length + 1" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-600">No orders found</p>
                                        <p class="text-sm text-slate-400 mt-1">Get started by creating your first order</p>
                                        <button @click="openCreateModal" class="mt-4 px-5 py-2.5 bg-gradient-to-r from-[#E67823] to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg shadow-orange-500/30 font-medium">
                                            Create Order
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="item in orders.data" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">#{{ item.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                            {{ item.branch?.name?.charAt(0).toUpperCase() || 'B' }}
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ item.branch?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-orange-500/30">
                                            {{ item.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                        </div>
                                        <span class="text-slate-700">{{ item.user?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">{{ formatCurrency(item.total_amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ formatCurrency(item.tax_amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">{{ formatCurrency(item.grand_total) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[getStatusClass(item.status), 'px-3 py-1.5 rounded-full text-xs font-semibold capitalize inline-flex items-center gap-1.5']">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="{
                                            'bg-amber-500': item.status === 'pending',
                                            'bg-blue-500': item.status === 'processing',
                                            'bg-emerald-500': item.status === 'completed',
                                            'bg-red-500': item.status === 'cancelled'
                                        }"></span>
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ formatDate(item.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(item)" class="p-2.5 text-[#E67823] hover:text-orange-700 hover:bg-orange-50 rounded-xl transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="handleDelete(item)" class="p-2.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition-colors" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="orders.last_page > 1" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Showing <span class="font-semibold">{{ orders.from }}</span> to <span class="font-semibold">{{ orders.to }}</span> of <span class="font-semibold">{{ orders.total }}</span> results
                    </div>
                    <div class="flex gap-1">
                        <a v-for="page in orders.last_page" :key="page" :href="`?page=${page}`" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" :class="page === orders.current_page ? 'bg-[#E67823] text-white shadow-lg shadow-orange-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'">
                            {{ page }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" :title="editingItem ? 'Edit Order' : 'Create New Order'" size="lg" @close="closeModal">
            <form @submit.prevent="handleSubmit" class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <FormInput v-model="form.branch_id" type="select" label="Branch" placeholder="Select branch" :options="branchOptions" :error="form.errors.branch_id" required />
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Created By</label>
                        <div class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-700">
                            {{ $page.props.auth.user.name }}
                        </div>
                    </div>
                </div>

                <!-- <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <div class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-700">
                        {{ editingItem ? form.status : 'Completed' }}
                    </div>
                </div> -->

                <!-- Order Items -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-slate-800">Order Items</h3>
                        <button type="button" @click="addItem" class="px-3 py-1.5 text-sm bg-[#E67823] text-white rounded-lg hover:bg-orange-600 transition-colors">
                            + Add Item
                        </button>
                    </div>
                    
                    <div v-for="(item, index) in form.items" :key="index" class="flex gap-3 mb-3 items-end">
                        <div class="flex-1">
                            <FormInput 
                                v-model="item.product_id" 
                                type="select" 
                                label="Product" 
                                placeholder="Select product" 
                                :options="productOptions" 
                                :error="form.errors[`items.${index}.product_id`] || ''"
                                required 
                                @update:modelValue="onProductSelect(item)"
                            />
                        </div>
                        <div class="w-24">
                            <FormInput 
                                v-model="item.quantity" 
                                type="number" 
                                label="Qty" 
                                placeholder="1" 
                                :error="form.errors[`items.${index}.quantity`] || ''"
                                :min="1"
                                :max="getAvailableQuantity(item.product_id)"
                                required 
                            />
                            <p v-if="item.product_id" class="text-xs text-slate-500 mt-1">
                                Max: {{ getAvailableQuantity(item.product_id) }}
                            </p>
                        </div>
                        <div class="w-32">
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>
                                <div class="w-full rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-3 text-slate-700">
                                    ${{ getProductPrice(item.product_id).toFixed(2) }}
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="removeItem(index)" class="p-3 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition-colors mb-5" :disabled="form.items.length <= 1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="flex justify-between items-center text-lg font-semibold">
                        <span>Grand Total:</span>
                        <span class="text-[#E67823]">${{ grandTotal.toFixed(2) }}</span>
                    </div>
                </div>

                <!-- <FormInput v-model="form.status" type="select" label="Status" placeholder="Select status" :options="statusOptions" :error="form.errors.status" required /> -->
            </form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button @click="closeModal" class="px-6 py-3 text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all font-semibold">
                        Cancel
                    </button>
                    <button @click="handleSubmit" class="px-6 py-3 text-white bg-gradient-to-r from-[#E67823] to-orange-600 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all font-semibold shadow-lg shadow-orange-500/30" :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>{{ editingItem ? 'Update Order' : 'Create Order' }}</span>
                    </button>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
