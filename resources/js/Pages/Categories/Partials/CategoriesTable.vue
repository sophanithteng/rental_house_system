<script setup>
defineProps({
    categories: Array,
    isAdmin: Boolean,
});

defineEmits(['edit', 'delete']);

const imageUrl = (path) => (path ? `/storage/${path}` : null);
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/70 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left">Photo</th>
                        <th scope="col" class="px-6 py-3.5 text-left">Category Name</th>
                        <th scope="col" class="px-6 py-3.5 text-left">Base Price</th>
                        <th scope="col" class="px-6 py-3.5 text-left">Description</th>
                        <th v-if="isAdmin" scope="col" class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    <tr v-for="item in categories" :key="item.id">
                        <td class="whitespace-nowrap px-6 py-4">
                            <img v-if="imageUrl(item.photo)" :src="imageUrl(item.photo)"
                                class="h-10 w-10 rounded-lg object-cover shadow-sm" alt="category" />
                            <div v-else
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                <i class="fas fa-tag"></i>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">{{ item.cat_name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 font-medium">${{ Number(item.base_price).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 line-clamp-1 max-w-xs">{{ item.description }}</td>
                        <td v-if="isAdmin" class="whitespace-nowrap px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-amber-600 transition-colors"
                                    @click="startEdit(item)">Edit</button>
                                <button
                                    class="inline-flex items-center rounded-lg bg-white border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-600 shadow-sm hover:bg-rose-50 transition-colors"
                                    @click="removeCategory(item.id)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
