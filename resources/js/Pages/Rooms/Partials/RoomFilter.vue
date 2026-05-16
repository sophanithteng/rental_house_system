<script setup>
defineProps({
    modelValue: [String, Number],
    search: String,
    categories: {
        type: Array,
        default: () => [],
    },
    isAdmin: Boolean,
    showForm: Boolean,
    viewMode: String,
    availableOnly: Boolean,
});
defineEmits(['update:modelValue', 'update:search', 'update:viewMode', 'update:availableOnly', 'change', 'toggle-form']);
</script>

<template>
    <div class="rounded border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-end">
            <!-- Search Input -->
            <div class="flex-1">
                <label class="mb-1 block text-xs font-bold uppercase text-slate-600">Search Rooms</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" :value="search" @input="$emit('update:search', $event.target.value)"
                        @keyup.enter="$emit('change')"
                        class="block w-full rounded border-slate-300 pl-9 text-sm focus:border-blue-500 focus:ring-0"
                        placeholder="Room number (e.g. A-101)" />
                </div>
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:max-w-xs">
                <label class="mb-1 block text-xs font-bold uppercase text-slate-600">Category</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                        <i class="fas fa-filter text-xs"></i>
                    </span>
                    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
                        @change="$emit('change')"
                        class="block w-full rounded border-slate-300 pl-9 text-sm focus:border-blue-500 focus:ring-0">
                        <option value="">All Categories</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.cat_name }}</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <!-- View Switcher -->
                <div class="flex items-center rounded-lg bg-slate-100 p-1 mr-2">
                    <button
                        type="button"
                        @click="$emit('update:viewMode', 'table')"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-md transition-all"
                        :class="viewMode === 'table' ? 'bg-white shadow-sm' : 'hover:bg-slate-200'"
                        title="Table View">
                        <img src="/images/list.png" class="h-7 w-7 shrink-0 rounded object-cover" alt="list" />
                    </button>
                    <button
                        type="button"
                        @click="$emit('update:viewMode', 'card')"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-md transition-all"
                        :class="viewMode === 'card' ? 'bg-white shadow-sm' : 'hover:bg-slate-200'"
                        title="Card View">
                        <img src="/images/card.png" class="h-7 w-7 shrink-0 rounded object-cover" alt="card" />
                    </button>
                </div>

                <button @click="$emit('change')"
                    class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-[9px] text-sm font-semibold text-white transition-colors hover:bg-blue-700 active:bg-blue-800 shadow-sm">
                    <i class="fas fa-search text-xs"></i>
                    <span>Search</span>
                </button>

                <button v-if="isAdmin && !showForm" @click="$emit('toggle-form', true)"
                    class="inline-flex items-center gap-2 rounded bg-indigo-600 px-4 py-[9px] text-sm font-semibold text-white transition-colors hover:bg-indigo-700 active:bg-indigo-800 shadow-sm whitespace-nowrap">
                    <i class="fas fa-plus text-xs"></i>
                    <span>New Room</span>
                </button>
            </div>
        </div>
    </div>
</template>
