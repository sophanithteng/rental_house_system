<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    editing: Object,
});

const emit = defineEmits(['success', 'cancel']);

const form = useForm({
    cat_name: '',
    base_price: '',
    description: '',
    photo: null,
});

watch(() => props.editing, (category) => {
    if (category) {
        form.cat_name = category.cat_name;
        form.base_price = category.base_price;
        form.description = category.description ?? '';
        form.photo = null;
    } else {
        form.reset();
        form.clearErrors();
    }
}, { immediate: true });

const submit = () => {
    if (props.editing) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(route('categories.update', props.editing.id), {
                forceFormData: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Category updated successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: '#4f46e5',
                    });
                    emit('success');
                },
            });
        return;
    }
    form.post(route('categories.store'), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'New category added successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#4f46e5',
            });
            emit('success');
        },
    });
};
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition-all duration-200">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span :class="editing ? 'bg-amber-500' : 'bg-indigo-500'" class="flex h-2.5 w-2.5 rounded-full"></span>
                {{ editing ? 'Update Category' : 'Create New Category' }}
            </h3>
        </div>

        <form class="p-6 grid gap-5 md:grid-cols-2" @submit.prevent="submit">
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Category Name <span
                        class="text-rose-500">*</span></label>
                <input v-model="form.cat_name" type="text"
                    class="w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., Luxury Suite" required />
                <p v-if="form.errors.cat_name" class="mt-1 text-xs font-medium text-rose-600">{{ form.errors.cat_name }}
                </p>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Base Price ($) <span
                        class="text-rose-500">*</span></label>
                <input v-model="form.base_price" type="number" step="0.01" min="0"
                    class="w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required />
                <p v-if="form.errors.base_price" class="mt-1 text-xs font-medium text-rose-600">{{
                    form.errors.base_price }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Description</label>
                <textarea v-model="form.description"
                    class="w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    rows="3"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Category Icon/Photo</label>
                <div
                    class="mt-1 flex items-center gap-4 rounded-lg border border-dashed border-slate-200 bg-slate-50/50 p-4">
                    <input type="file" accept="image/*"
                        class="text-sm text-slate-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700"
                        @change="form.photo = $event.target.files[0]" />
                </div>
            </div>
            <div class="md:col-span-2 flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                    @click="$emit('cancel')">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Back to Index</span>
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors shadow-sm"
                    :class="editing ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700'"
                    :disabled="form.processing">
                    {{ editing ? 'Save Changes' : 'Add Category' }}
                </button>
            </div>
        </form>
    </div>
</template>
