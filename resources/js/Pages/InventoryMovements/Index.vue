<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import Swal, { showSuccess, showDeleteConfirm } from '@/Plugins/sweetalert';

const props = defineProps({
    movements: Object,
    branches: Array,
    products: Array,
});

const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    branch_id: '',
    product_id: '',
    type: 'in',
    quantity_change: '',
    notes: '',
});

const columns = [
    { key: 'id', label: 'ID', class: 'w-16' },
    { key: 'branch', label: 'Branch' },
    { key: 'product', label: 'Product' },
    { key: 'type', label: 'Type' },
    { key: 'quantity_change', label: 'Quantity' },
    { key: 'user', label: 'User' },
    { key: 'notes', label: 'Notes' },
    { key: 'created_at', label: 'Date' },
];

const branchOptions = computed(() => 
    props.branches?.map(b => ({ value: b.id, label: b.name })) || []
);

const productOptions = computed(() => 
    props.products?.map(p => ({ value: p.id, label: `${p.name} (${p.sku})` })) || []
);

const typeOptions = [
    { value: 'in', label: 'Stock In' },
    { value: 'out', label: 'Stock Out' },
    { value: 'adjustment', label: 'Adjustment' },
    { value: 'addition', label: 'Addition' },
    { value: 'reduction', label: 'Reduction' },
];

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.branch_id = '';
    form.product_id = '';
    form.type = 'in';
    form.quantity_change = '';
    form.notes = '';
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.branch_id = item.branch_id;
    form.product_id = item.product_id;
    form.type = item.type;
    form.quantity_change = item.quantity_change;
    form.notes = item.notes;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingItem.value = null;
};

const handleSubmit = () => {
    if (editingItem.value) {
        form.put(`/inventory-movements/${editingItem.value.id}`, {
            onSuccess: () => {
                showSuccess('Inventory movement updated successfully');
                closeModal();
            },
        });
    } else {
        form.post('/inventory-movements', {
            onSuccess: () => {
                showSuccess('Inventory movement recorded successfully');
                closeModal();
            },
        });
    }
};

const handleDelete = (item) => {
    showDeleteConfirm(`Movement #${item.id}`).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/inventory-movements/${item.id}`, {
                onSuccess: () => {
                    showSuccess('Inventory movement deleted successfully');
                },
            });
        }
    });
};

const getTypeClass = (type) => {
    const classes = {
        in: 'bg-emerald-100 text-emerald-700',
        out: 'bg-red-100 text-red-700',
        addition: 'bg-emerald-100 text-emerald-700',
        reduction: 'bg-red-100 text-red-700',
        adjustment: 'bg-blue-100 text-blue-700',
    };
    return classes[type] || 'bg-slate-100 text-slate-700';
};

const getTypeLabel = (type) => {
    const labels = {
        in: 'Stock In',
        out: 'Stock Out',
        addition: 'Stock In',
        reduction: 'Stock Out',
        adjustment: 'Adjustment',
    };
    return labels[type] || type;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStockIn = computed(() => {
    return props.movements?.data?.filter(item => item.type === 'in' || item.type === 'addition').reduce((sum, item) => sum + (parseInt(item.quantity_change) || 0), 0) || 0;
});

const getStockOut = computed(() => {
    return props.movements?.data?.filter(item => item.type === 'out' || item.type === 'reduction').reduce((sum, item) => sum + (parseInt(item.quantity_change) || 0), 0) || 0;
});

const getTotalMovements = computed(() => {
    return props.movements?.total || 0;
});

const getQuantitySign = (type) => {
    return (type === 'in' || type === 'addition') ? '+' : '-';
};
</script>

<template>
    <AppLayout title="Inventory Movements">
        <!-- <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Inventory Movements
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Track stock in and out movements</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-gradient-to-r from-[#E67823] to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="font-semibold">Add Movement</span>
                </button>
            </div>
        </template> -->

        <div class="py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Movements</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ getTotalMovements }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#E67823]/10 to-orange-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#E67823]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Stock In</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-2">+{{ getStockIn }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Stock Out</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">-{{ getStockOut }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
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
                                <!-- <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th> -->
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            <tr v-if="movements.data.length === 0">
                                <td :colspan="columns.length + 1" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-600">No movements found</p>
                                        <p class="text-sm text-slate-400 mt-1">Get started by recording your first movement</p>
                                        <button @click="openCreateModal" class="mt-4 px-5 py-2.5 bg-gradient-to-r from-[#E67823] to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg shadow-orange-500/30 font-medium">
                                            Add Movement
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="item in movements.data" :key="item.id" class="hover:bg-slate-50 transition-colors">
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
                                    <span :class="[getTypeClass(item.type), 'px-3 py-1.5 rounded-full text-xs font-semibold capitalize inline-flex items-center gap-1.5']">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="{
                                            'bg-emerald-500': item.type === 'in' || item.type === 'addition',
                                            'bg-red-500': item.type === 'out' || item.type === 'reduction',
                                            'bg-blue-500': item.type === 'adjustment'
                                        }"></span>
                                        {{ getTypeLabel(item.type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold" 
                                        :class="(item.type === 'in' || item.type === 'addition') ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                                        {{ getQuantitySign(item.type) }}{{ item.quantity_change }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#E67823]/20 to-orange-100 flex items-center justify-center text-[#E67823] font-bold text-xs">
                                            {{ item.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                        </div>
                                        <span class="text-sm text-slate-600">{{ item.user?.name || '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 max-w-xs truncate">
                                    {{ item.notes || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ formatDate(item.created_at) }}</td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="movements.last_page > 1" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Showing <span class="font-semibold">{{ movements.from }}</span> to <span class="font-semibold">{{ movements.to }}</span> of <span class="font-semibold">{{ movements.total }}</span> results
                    </div>
                    <div class="flex gap-1">
                        <a v-for="page in movements.last_page" :key="page" :href="`?page=${page}`" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" :class="page === movements.current_page ? 'bg-[#E67823] text-white shadow-lg shadow-orange-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'">
                            {{ page }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" :title="editingItem ? 'Edit Movement' : 'Record New Movement'" size="md" @close="closeModal">
            <form @submit.prevent="handleSubmit" class="space-y-5">
                <FormInput v-model="form.branch_id" type="select" label="Branch" placeholder="Select branch" :options="branchOptions" :error="form.errors.branch_id" required />
                <FormInput v-model="form.product_id" type="select" label="Product" placeholder="Select product" :options="productOptions" :error="form.errors.product_id" required />
                <FormInput v-model="form.type" type="select" label="Movement Type" placeholder="Select type" :options="typeOptions" :error="form.errors.type" required />
                <FormInput v-model="form.quantity_change" type="number" label="Quantity Change" placeholder="Enter quantity (+ or -)" :error="form.errors.quantity_change" required />
                <FormInput v-model="form.notes" type="textarea" label="Notes" placeholder="Enter notes (optional)" :error="form.errors.notes" rows="3" />
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
                        <span v-else>{{ editingItem ? 'Update Movement' : 'Record Movement' }}</span>
                    </button>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
