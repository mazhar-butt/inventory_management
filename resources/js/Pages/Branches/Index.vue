<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import Swal, { showSuccess, showDeleteConfirm } from '@/Plugins/sweetalert';

const props = defineProps({
    branches: Object,
    users: Array,
    managers: Array,
});

const page = usePage();

// Get the URL prefix based on user role
const getUrlPrefix = () => {
    const role = page.props.auth?.user?.role;
    if (role === 'admin') return '/admin';
    return '/admin'; // default
};

const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    name: '',
    address: '',
    is_active: true,
    manager_id: '',
});

const columns = [
    { key: 'id', label: 'ID', class: 'w-16' },
    { key: 'name', label: 'Name' },
    { key: 'address', label: 'Address' },
    { key: 'manager', label: 'Manager' },
    { key: 'is_active', label: 'Status' },
    { key: 'created_at', label: 'Created At' },
];

const userOptions = computed(() => {
    // Use managers if available, otherwise fall back to users
    const managerList = props.managers || props.users;
    return managerList?.map(user => ({
        value: user.id,
        label: user.name
    })) || [];
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.name = '';
    form.address = '';
    form.is_active = true;
    form.manager_id = '';
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.address = item.address;
    form.is_active = item.is_active;
    form.manager_id = item.manager_id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingItem.value = null;
};

const handleSubmit = () => {
    if (editingItem.value) {
        const prefix = getUrlPrefix();
        form.put(`${prefix}/branches/${editingItem.value.id}`, {
            onSuccess: () => {
                showSuccess('Branch updated successfully');
                closeModal();
            },
        });
    } else {
        const prefix = getUrlPrefix();
        form.post(`${prefix}/branches`, {
            onSuccess: () => {
                showSuccess('Branch created successfully');
                closeModal();
            },
        });
    }
};

const handleDelete = (item) => {
    showDeleteConfirm(item.name).then((result) => {
        if (result.isConfirmed) {
            const prefix = getUrlPrefix();
            form.delete(`${prefix}/branches/${item.id}`, {
                onSuccess: () => {
                    showSuccess('Branch deleted successfully');
                },
            });
        }
    });
};

const getStatusClass = (isActive) => {
    return isActive 
        ? 'bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold' 
        : 'bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <AppLayout title="Branches">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Branch Management
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Manage your branches</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-gradient-to-r from-[#E67823] to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="font-semibold">Add Branch</span>
                </button>
            </div>
        </template>

        <div class="py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Branches</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">{{ branches.total || 0 }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#E67823]/10 to-orange-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#E67823]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Active Branches</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ branches.data.filter(b => b.is_active).length || 0 }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Inactive Branches</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ branches.data.filter(b => !b.is_active).length || 0 }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <tr v-if="branches.data.length === 0">
                                <td :colspan="columns.length + 1" class="px-6 py-20 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-600">No branches found</p>
                                        <p class="text-sm text-slate-400 mt-1">Get started by creating your first branch</p>
                                        <button @click="openCreateModal" class="mt-4 px-5 py-2.5 bg-gradient-to-r from-[#E67823] to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg shadow-orange-500/30 font-medium">
                                            Create Branch
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="item in branches.data" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">#{{ item.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#E67823] to-orange-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-orange-500/30">
                                            {{ item.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ item.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ item.address || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="item.manager" class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">
                                            {{ item.manager.name.charAt(0) }}
                                        </div>
                                        <span class="text-sm text-slate-600">{{ item.manager.name }}</span>
                                    </div>
                                    <span v-else class="text-sm text-slate-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(item.is_active)">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                            {{ item.is_active ? 'Active' : 'Inactive' }}
                                        </span>
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
                <div v-if="branches.last_page > 1" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Showing <span class="font-semibold">{{ branches.from }}</span> to <span class="font-semibold">{{ branches.to }}</span> of <span class="font-semibold">{{ branches.total }}</span> results
                    </div>
                    <div class="flex gap-1">
                        <a v-for="page in branches.last_page" :key="page" :href="`?page=${page}`" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" :class="page === branches.current_page ? 'bg-[#E67823] text-white shadow-lg shadow-orange-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'">
                            {{ page }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" :title="editingItem ? 'Edit Branch' : 'Create New Branch'" size="xs" @close="closeModal">
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <FormInput 
                        v-model="form.name" 
                        label="Branch Name" 
                        placeholder="Enter branch name" 
                        :error="form.errors.name" 
                        required 
                    />
                    
                    <FormInput 
                        v-model="form.manager_id" 
                        type="select" 
                        label="Manager" 
                        placeholder="Select manager" 
                        :options="userOptions" 
                        :error="form.errors.manager_id" 
                    />
                </div>
                
                <FormInput 
                    v-model="form.address" 
                    label="Address" 
                    placeholder="Enter branch address" 
                    :error="form.errors.address" 
                    type="textarea"
                />
                
                <div class="flex items-center justify-between py-3 px-4 bg-slate-50 rounded-xl border-2 border-slate-200">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Active Status</label>
                        <p class="text-xs text-slate-500 mt-0.5">Enable or disable this branch</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#E67823]"></div>
                    </label>
                </div>
            </form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button 
                        @click="closeModal" 
                        class="px-5 py-2.5 text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all font-semibold text-sm"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="handleSubmit" 
                        class="px-5 py-2.5 text-white bg-gradient-to-r from-[#E67823] to-orange-600 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all font-semibold text-sm shadow-lg shadow-orange-500/30"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>{{ editingItem ? 'Update Branch' : 'Create Branch' }}</span>
                    </button>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
