<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import CategoriesTable from './Partials/CategoriesTable.vue';
import CategoriesForm from './Partials/CategoriesForm.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

const editing = ref(null);
const showForm = ref(false);

const resetForm = () => {
    editing.value = null;
    showForm.value = false;
};

const startAdd = () => {
    editing.value = null;
    showForm.value = true;
};

const startEdit = (item) => {
    editing.value = item;
    showForm.value = true;
};

const removeCategory = (id) => {
    if (!confirm('Delete this category?')) return;
    router.delete(route('categories.destroy', id));
};
</script>

<template>

    <Head title="Categories" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold tracking-tight text-slate-800">Categories Management</h2>
        </template>

        <div class="py-6 bg-slate-50 min-h-screen">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Action Buttons -->
                <div v-if="isAdmin && !showForm" class="flex items-center justify-between">
                    <nav class="flex items-center gap-2 text-sm font-medium" aria-label="Breadcrumb">
                        <Link :href="route('dashboard')" class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-home text-xs"></i>
                        </Link>
                        <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
                        <span class="text-slate-700">Manage Categories</span>
                    </nav>
                    <button @click="startAdd" type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200">
                        <i class="fas fa-plus text-xs"></i>
                        <span>Add New Category</span>
                    </button>
                </div>

                <!-- View: Table List -->
                <template v-if="!showForm">
                    <CategoriesTable :categories="categories" :is-admin="isAdmin" @edit="startEdit"
                        @delete="removeCategory" />
                </template>

                <!-- View: Form -->
                <template v-else>
                    <CategoriesForm v-if="isAdmin" :editing="editing" @success="resetForm" @cancel="resetForm" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
