<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
const form = useForm({
    cat_name: '',
    base_price: '',
    description: '',
    photo: null,
});

const imageUrl = (path) => (path ? `/storage/${path}` : null);

const resetForm = () => {
    form.reset();
    form.clearErrors();
    editing.value = null;
};

const startEdit = (item) => {
    editing.value = item;
    form.cat_name = item.cat_name;
    form.base_price = item.base_price;
    form.description = item.description ?? '';
    form.photo = null;
};

const submit = () => {
    if (editing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('categories.update', editing.value.id), {
            forceFormData: true,
            onSuccess: resetForm,
        });
        return;
    }
    form.post(route('categories.store'), {
        forceFormData: true,
        onSuccess: resetForm,
    });
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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">ប្រភេទបន្ទប់</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div v-if="isAdmin" class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold">{{ editing ? 'កែប្រែប្រភេទ' : 'បន្ថែមប្រភេទថ្មី' }}</h3>
                    <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                        <div>
                            <label class="mb-1 block text-sm">ឈ្មោះប្រភេទ</label>
                            <input v-model="form.cat_name" class="w-full rounded-md border-gray-300" type="text" required />
                            <p v-if="form.errors.cat_name" class="mt-1 text-sm text-rose-600">{{ form.errors.cat_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">តម្លៃមូលដ្ឋាន</label>
                            <input v-model="form.base_price" class="w-full rounded-md border-gray-300" type="number" min="0" step="0.01" required />
                            <p v-if="form.errors.base_price" class="mt-1 text-sm text-rose-600">{{ form.errors.base_price }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm">ការពិពណ៌នា</label>
                            <textarea v-model="form.description" class="w-full rounded-md border-gray-300" rows="3"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm">រូបភាព</label>
                            <input type="file" accept="image/*" @change="form.photo = $event.target.files[0]" />
                            <p v-if="form.errors.photo" class="mt-1 text-sm text-rose-600">{{ form.errors.photo }}</p>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" :disabled="form.processing">
                                {{ editing ? 'កែប្រែ' : 'បន្ថែម' }}
                            </button>
                            <button v-if="editing" type="button" class="rounded-md border px-4 py-2" @click="resetForm">បោះបង់</button>
                        </div>
                    </form>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-3 py-2 text-left">រូបភាព</th>
                                    <th class="border px-3 py-2 text-left">ឈ្មោះ</th>
                                    <th class="border px-3 py-2 text-left">តម្លៃ</th>
                                    <th class="border px-3 py-2 text-left">ពិពណ៌នា</th>
                                    <th v-if="isAdmin" class="border px-3 py-2 text-left">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in categories" :key="item.id">
                                    <td class="border px-3 py-2">
                                        <img v-if="imageUrl(item.photo)" :src="imageUrl(item.photo)" class="h-12 w-12 rounded object-cover" alt="category" />
                                    </td>
                                    <td class="border px-3 py-2">{{ item.cat_name }}</td>
                                    <td class="border px-3 py-2">${{ Number(item.base_price).toFixed(2) }}</td>
                                    <td class="border px-3 py-2">{{ item.description }}</td>
                                    <td v-if="isAdmin" class="border px-3 py-2">
                                        <div class="flex gap-2">
                                            <button class="rounded bg-amber-500 px-2 py-1 text-white" @click="startEdit(item)">Edit</button>
                                            <button class="rounded bg-rose-600 px-2 py-1 text-white" @click="removeCategory(item.id)">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
