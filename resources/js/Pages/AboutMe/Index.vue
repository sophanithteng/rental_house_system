<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tenant: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    full_name: props.tenant?.full_name ?? '',
    phone: props.tenant?.phone ?? '',
    id_card: props.tenant?.id_card ?? '',
    address_origin: props.tenant?.address_origin ?? '',
});

const submit = () => {
    form.put(route('about-me.update'));
};
</script>

<template>
    <Head title="About Me" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">អំពីខ្ញុំ</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">ឈ្មោះពេញ</label>
                            <input v-model="form.full_name" type="text" class="w-full rounded-md border-gray-300" required />
                            <p v-if="form.errors.full_name" class="mt-1 text-sm text-rose-600">{{ form.errors.full_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">លេខទូរស័ព្ទ</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-md border-gray-300" required />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-rose-600">{{ form.errors.phone }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">អត្តសញ្ញាណប័ណ្ណ</label>
                            <input v-model="form.id_card" type="text" class="w-full rounded-md border-gray-300" required />
                            <p v-if="form.errors.id_card" class="mt-1 text-sm text-rose-600">{{ form.errors.id_card }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">អាសយដ្ឋាន</label>
                            <input v-model="form.address_origin" type="text" class="w-full rounded-md border-gray-300" required />
                            <p v-if="form.errors.address_origin" class="mt-1 text-sm text-rose-600">{{ form.errors.address_origin }}</p>
                        </div>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" :disabled="form.processing">
                            រក្សាទុក
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
