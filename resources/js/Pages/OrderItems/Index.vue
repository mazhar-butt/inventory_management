<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import Swal, { showSuccess, showDeleteConfirm } from '@/Plugins/sweetalert';

const props = defineProps({
    items: Object,
    orders: Array,
    products: Array,
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
    order_id: '',
    product_id: '',
    quantity: '',
    price: '',
});

const columns = [
    { key: 'id', label: 'ID', class: 'w-16' },
    { key: 'order', label: 'Order' },
    { key: 'product', label: 'Product' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'price', label: 'Price' },
    { key: 'total', label: 'Total' },
    { key: 'created_at', label: 'Created At' },
];

const orderOptions = computed(() => 
    props.orders?.map(o => ({ value: o.id, label: `#${o.id} - ${o.branch?.name || ''}` })) || []
);

const productOptions = computed(() => 
    props.products?.map(p => ({ value: p.id, label: `${p.name} (${p.sku})` })) || []
);

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.order_id = '';
    form.product_id = '';
    form.quantity = '';
    form.price = '';
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.order_id = item.order_id;
    form.product_id = item.product_id;
    form.quantity = item.quantity;
    form.price = item.price;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingItem.value = null;
};

const calculateTotal = () => {
    const qty = parseInt(form.quantity) || 0;
    const price = parseFloat(form.price) || 0;
    return (qty * price).toFixed(2);
};

const handleSubmit = () => {
    if (editingItem.value) {
        const prefix = getUrlPrefix();
        form.put(`${prefix}/order-items/${editingItem.value.id}`, {
            onSuccess: () => {
                showSuccess('Order item updated successfully');
                closeModal();
            },
        });
    } else {
        const prefix = getUrlPrefix();
        form.post(`${prefix}/order-items`, {
            onSuccess: () => {
                showSuccess('Order item created successfully');
                closeModal();
            },
        });
    }
};

const handleDelete = (item) => {
    showDeleteConfirm(`Order Item #${item.id}`).then((result) => {
        if (result.isConfirmed) {
            const prefix = getUrlPrefix();
            form.delete(`${prefix}/order-items/${item.id}`, {
                onSuccess: () => {
                    showSuccess('Order item deleted successfully');
                },
            });
        }
    });
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

const getTotalItems = computed(() => {
    return props.items?.data?.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0) || 0;
});

const getTotalValue = computed(() => {
    return props.items?.data?.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseInt(item.quantity) || 0)), 0) || 0;
});
</script>

<template>
    <AppLayout title="Order Items">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Order Items Management
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Manage items within orders</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-gradient-to-r from-[#E67823] to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="font-semibold">Add Item</span>
                </button>
            </div>
        </template>

        <div class="py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Items</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ items.total || 0 }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#E67823]/10 to-orange-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#E67823]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Quantity</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ getTotalItems }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Value</p>
                            <p class="text-3xl font-bold text-[#E67823] mt-2">{{ formatCurrency(getTotalValue) }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#E67823]/10 to-orange-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#E67823]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <tr v-if="items.data.length === 0">
                                <td :colspan="columns.length + 1" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-600">No order items found</p>
                                        <p class="text-sm text-slate-400 mt-1">Get started by adding your first item</p>
                                        <button @click="openCreateModal" class="mt-4 px-5 py-2.5 bg-gradient-to-r from-[#E67823] to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg shadow-orange-500/30 font-medium">
                                            Add Item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="item in items.data" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">#{{ item.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                            #
                                        </div>
                                        <span class="font-semibold text-slate-800">Order {{ item.order_id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-orange-500/30">
                                            {{ item.product?.name?.charAt(0).toUpperCase() || 'P' }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ item.product?.name || '-' }}</p>
                                            <p class="text-xs text-slate-500 font-mono">{{ item.product?.sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-bold bg-slate-100 text-slate-700">
                                        {{ item.quantity }} units
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ formatCurrency(item.price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">{{ formatCurrency(item.quantity * item.price) }}</td>
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
                <div v-if="items.last_page > 1" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Showing <span class="font-semibold">{{ items.from }}</span> to <span class="font-semibold">{{ items.to }}</span> of <span class="font-semibold">{{ items.total }}</span> results
                    </div>
                    <div class="flex gap-1">
                        <a v-for="page in items.last_page" :key="page" :href="`?page=${page}`" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" :class="page === items.current_page ? 'bg-[#E67823] text-white shadow-lg shadow-orange-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'">
                            {{ page }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" :title="editingItem ? 'Edit Order Item' : 'Add New Order Item'" size="md" @close="closeModal">
            <form @submit.prevent="handleSubmit" class="space-y-5">
                <FormInput v-model="form.order_id" type="select" label="Order" placeholder="Select order" :options="orderOptions" :error="form.errors.order_id" required />
                <FormInput v-model="form.product_id" type="select" label="Product" placeholder="Select product" :options="productOptions" :error="form.errors.product_id" required />
                <FormInput v-model="form.quantity" type="number" label="Quantity" placeholder="Enter quantity" :error="form.errors.quantity" required />
                <FormInput v-model="form.price" type="number" step="0.01" label="Unit Price" placeholder="Enter price" :error="form.errors.price" required @update:modelValue="calculateTotal" />
                <div class="p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-emerald-700">Line Total:</span>
                        <span class="text-2xl font-bold text-emerald-600">{{ formatCurrency(calculateTotal()) }}</span>
                    </div>
                </div>
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
                        <span v-else>{{ editingItem ? 'Update Item' : 'Add Item' }}</span>
                    </button>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
