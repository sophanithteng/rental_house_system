<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: { type: Array, required: true },
    currentUserId: { type: Number, required: true },
});

const editing = ref(null);
const form = useForm({
    username: '',
    role: 'User',
    password: '',
    password_confirmation: '',
    photo: null,
    full_name: '',
    phone: '',
    id_card: '',
    address_origin: '',
});

const imageUrl = (path) => (path ? `/storage/${path}` : null);

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.role = 'User';
    editing.value = null;
};

const startEdit = (user) => {
    editing.value = user;
    form.username = user.username;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
    form.photo = null;
    form.full_name = user.tenant?.full_name ?? '';
    form.phone = user.tenant?.phone ?? '';
    form.id_card = user.tenant?.id_card ?? '';
    form.address_origin = user.tenant?.address_origin ?? '';
};

const submit = () => {
    if (editing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('users.update', editing.value.id), {
            forceFormData: true,
            onSuccess: resetForm,
        });
        return;
    }
    form.post(route('users.store'), {
        forceFormData: true,
        onSuccess: resetForm,
    });
};

const removeUser = (user) => {
    if (Number(user.id) === Number(props.currentUserId)) {
        alert('You cannot delete your own account.');
        return;
    }
    if (!confirm(`Delete user ${user.username}?`)) return;
    router.delete(route('users.destroy', user.id));
};
</script>

<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">គ្រប់គ្រងអ្នកប្រើប្រាស់</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold">{{ editing ? 'កែប្រែអ្នកប្រើប្រាស់' : 'បន្ថែមអ្នកប្រើប្រាស់' }}</h3>
                    <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                        <div>
                            <label class="mb-1 block text-sm">Username</label>
                            <input v-model="form.username" class="w-full rounded-md border-gray-300" type="text" required />
                            <p v-if="form.errors.username" class="mt-1 text-sm text-rose-600">{{ form.errors.username }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Role</label>
                            <select v-model="form.role" class="w-full rounded-md border-gray-300" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                                <option value="SuperAdmin">SuperAdmin</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Password {{ editing ? '(optional)' : '' }}</label>
                            <input v-model="form.password" class="w-full rounded-md border-gray-300" type="password" :required="!editing" />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-rose-600">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Confirm Password</label>
                            <input v-model="form.password_confirmation" class="w-full rounded-md border-gray-300" type="password" :required="!editing" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Full Name</label>
                            <input v-model="form.full_name" class="w-full rounded-md border-gray-300" type="text" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Phone</label>
                            <input v-model="form.phone" class="w-full rounded-md border-gray-300" type="text" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">ID Card</label>
                            <input v-model="form.id_card" class="w-full rounded-md border-gray-300" type="text" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">Address</label>
                            <input v-model="form.address_origin" class="w-full rounded-md border-gray-300" type="text" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm">Photo</label>
                            <input type="file" accept="image/*" @change="form.photo = $event.target.files[0]" />
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
                                    <th class="border px-3 py-2 text-left">Photo</th>
                                    <th class="border px-3 py-2 text-left">Username</th>
                                    <th class="border px-3 py-2 text-left">Role</th>
                                    <th class="border px-3 py-2 text-left">Full Name</th>
                                    <th class="border px-3 py-2 text-left">Phone</th>
                                    <th class="border px-3 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td class="border px-3 py-2">
                                        <img v-if="imageUrl(user.photo)" :src="imageUrl(user.photo)" alt="user" class="h-10 w-10 rounded-full object-cover" />
                                    </td>
                                    <td class="border px-3 py-2">{{ user.username }}</td>
                                    <td class="border px-3 py-2">{{ user.role }}</td>
                                    <td class="border px-3 py-2">{{ user.tenant?.full_name ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ user.tenant?.phone ?? '-' }}</td>
                                    <td class="border px-3 py-2">
                                        <div class="flex gap-2">
                                            <button class="rounded bg-amber-500 px-2 py-1 text-white" @click="startEdit(user)">Edit</button>
                                            <button class="rounded bg-rose-600 px-2 py-1 text-white" @click="removeUser(user)">Delete</button>
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
