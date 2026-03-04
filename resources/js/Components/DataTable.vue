<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    actions: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['edit', 'delete', 'view']);

const filteredData = ref(props.data.data);

watch(() => props.data, (newData) => {
    filteredData.value = newData.data;
}, { deep: true });

const handleEdit = (item) => {
    emit('edit', item);
};

const handleDelete = (item) => {
    emit('delete', item);
};

const handleView = (item) => {
    emit('view', item);
};
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="column.class"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="actions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="filteredData.length === 0">
                        <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-lg font-medium">No data available</p>
                                <p class="text-sm text-gray-400">Start by creating a new record</p>
                            </div>
                        </td>
                    </tr>
                    <tr
                        v-for="(item, index) in filteredData"
                        :key="item.id"
                        class="hover:bg-gray-50 transition-colors"
                        :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap text-sm"
                            :class="column.class"
                        >
                            <slot :name="column.key" :item="item">
                                {{ item[column.key] }}
                            </slot>
                        </td>
                        <td v-if="actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    @click="handleView(item)"
                                    class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="View"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button
                                    @click="handleEdit(item)"
                                    class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="handleDelete(item)"
                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <div v-if="data.last_page > 1" class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ data.from }} to {{ data.to }} of {{ data.total }} results
            </div>
            <div class="flex gap-1">
                <Link
                    v-if="data.prev_page_url"
                    :href="data.prev_page_url"
                    class="px-3 py-1 rounded border border-gray-300 text-sm hover:bg-gray-50"
                >
                    Previous
                </Link>
                <Link
                    v-for="page in data.last_page"
                    :key="page"
                    :href="`?page=${page}`"
                    class="px-3 py-1 rounded border border-gray-300 text-sm hover:bg-gray-50"
                    :class="{ 'bg-blue-500 text-white border-blue-500': page === data.current_page }"
                >
                    {{ page }}
                </Link>
                <Link
                    v-if="data.next_page_url"
                    :href="data.next_page_url"
                    class="px-3 py-1 rounded border border-gray-300 text-sm hover:bg-gray-50"
                >
                    Next
                </Link>
            </div>
        </div>
    </div>
</template>
