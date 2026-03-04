<script setup>
defineProps({
    label: {
        type: String,
        default: '',
    },
    modelValue: {
        type: [String, Number, Boolean],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
    },
    optionLabel: {
        type: String,
        default: 'label',
    },
    optionValue: {
        type: String,
        default: 'value',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="mb-5">
        <label v-if="label" class="block text-sm font-semibold text-slate-700 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <select
            v-if="type === 'select'"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-slate-700 focus:border-[#E67823] focus:outline-none focus:ring-4 focus:ring-[#E67823]/10 transition-all duration-200"
            :class="{ 'border-red-300 bg-red-50': error, 'bg-slate-50': disabled }"
        >
            <option value="" disabled>{{ placeholder || 'Select an option' }}</option>
            <option
                v-for="option in options"
                :key="option[optionValue]"
                :value="option[optionValue]"
            >
                {{ option[optionLabel] }}
            </option>
        </select>
        
        <textarea
            v-else-if="type === 'textarea'"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            :placeholder="placeholder"
            rows="4"
            class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-slate-700 focus:border-[#E67823] focus:outline-none focus:ring-4 focus:ring-[#E67823]/10 transition-all duration-200 resize-none"
            :class="{ 'border-red-300 bg-red-50': error, 'bg-slate-50': disabled }"
        ></textarea>
        
        <div v-else-if="type === 'checkbox'">
            <label class="flex items-center p-4 bg-slate-50 rounded-xl border-2 border-slate-200 hover:border-[#E67823]/50 transition-colors cursor-pointer">
                <input
                    type="checkbox"
                    :checked="modelValue"
                    @change="$emit('update:modelValue', $event.target.checked)"
                    :disabled="disabled"
                    class="h-5 w-5 rounded border-slate-300 text-[#E67823] focus:ring-[#E67823] focus:ring-offset-2"
                />
                <span class="ml-3 text-sm text-slate-600">{{ placeholder }}</span>
            </label>
        </div>
        
        <input
            v-else
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            :readonly="readonly"
            :placeholder="placeholder"
            class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-slate-700 focus:border-[#E67823] focus:outline-none focus:ring-4 focus:ring-[#E67823]/10 transition-all duration-200"
            :class="{ 'border-red-300 bg-red-50': error, 'bg-slate-50': disabled }"
        />
        
        <p v-if="error" class="mt-2 text-sm text-red-500 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ error }}
        </p>
    </div>
</template>
