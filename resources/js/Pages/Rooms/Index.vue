<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    rooms: { type: Array, required: true },
    categories: { type: Array, required: true },
    filters: { type: Object, required: true },
    isAdmin: { type: Boolean, default: false },
    hasRequiredInfo: { type: Boolean, default: false },
});

const selectedCategory = ref(props.filters.category_id || '');
const editing = ref(null);

const form = useForm({
    room_number: '',
    category_id: '',
    price: '',
    status: 'ទំនេរ',
    description: '',
    photo: null,
});

const bookingForms = ref({});

const imageUrl = (path) => (path ? `/storage/${path}` : null);

const filteredRooms = computed(() => {
    if (!selectedCategory.value) return props.rooms;
    return props.rooms.filter((r) => Number(r.category_id) === Number(selectedCategory.value));
});

const onFilterChange = () => {
    router.get(route('rooms.index'), { category_id: selectedCategory.value || null }, { preserveState: true, replace: true });
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.status = 'ទំនេរ';
    editing.value = null;
};

const startEdit = (room) => {
    editing.value = room;
    form.room_number = room.room_number;
    form.category_id = room.category_id;
    form.price = room.price;
    form.status = room.status;
    form.description = room.description ?? '';
    form.photo = null;
};

const submit = () => {
    if (editing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('rooms.update', editing.value.id), {
            forceFormData: true,
            onSuccess: resetForm,
        });
        return;
    }
    form.post(route('rooms.store'), {
        forceFormData: true,
        onSuccess: resetForm,
    });
};

const removeRoom = (id) => {
    if (!confirm('Delete this room?')) return;
    router.delete(route('rooms.destroy', id));
};

const getBookingForm = (roomId) => {
    if (!bookingForms.value[roomId]) {
        bookingForms.value[roomId] = useForm({
            move_in_date: '',
        });
    }
    return bookingForms.value[roomId];
};

const bookRoom = (roomId) => {
    const bookForm = getBookingForm(roomId);
    bookForm.post(route('rooms.book', roomId));
};
</script>

<template>
    <Head title="Rooms" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">បន្ទប់ជួល</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-4 shadow">
                    <label class="mb-1 block text-sm">Filter by Category</label>
                    <select v-model="selectedCategory" class="rounded-md border-gray-300" @change="onFilterChange">
                        <option value="">All Categories</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.cat_name }}</option>
                    </select>
                </div>

                <div v-if="isAdmin" class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold">{{ editing ? 'កែប្រែបន្ទប់' : 'បន្ថែមបន្ទប់ថ្មី' }}</h3>
                    <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                        <div>
                            <label class="mb-1 block text-sm">លេខបន្ទប់</label>
                            <input v-model="form.room_number" type="text" class="w-full rounded-md border-gray-300" required />
                            <p v-if="form.errors.room_number" class="mt-1 text-sm text-rose-600">{{ form.errors.room_number }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">ប្រភេទ</label>
                            <select v-model="form.category_id" class="w-full rounded-md border-gray-300" required>
                                <option value="">Select</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.cat_name }}</option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-rose-600">{{ form.errors.category_id }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">តម្លៃ</label>
                            <input v-model="form.price" type="number" step="0.01" min="0" class="w-full rounded-md border-gray-300" required />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm">ស្ថានភាព</label>
                            <select v-model="form.status" class="w-full rounded-md border-gray-300" required>
                                <option value="ទំនេរ">ទំនេរ</option>
                                <option value="ជួលហើយ">ជួលហើយ</option>
                                <option value="ជួសជុល">ជួសជុល</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm">ពិពណ៌នា</label>
                            <textarea v-model="form.description" class="w-full rounded-md border-gray-300" rows="3"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm">រូបភាព</label>
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
                                    <th class="border px-3 py-2 text-left">រូបភាព</th>
                                    <th class="border px-3 py-2 text-left">បន្ទប់</th>
                                    <th class="border px-3 py-2 text-left">ប្រភេទ</th>
                                    <th class="border px-3 py-2 text-left">តម្លៃ</th>
                                    <th class="border px-3 py-2 text-left">ស្ថានភាព</th>
                                    <th class="border px-3 py-2 text-left">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="room in filteredRooms" :key="room.id">
                                    <td class="border px-3 py-2">
                                        <img v-if="imageUrl(room.photo)" :src="imageUrl(room.photo)" class="h-12 w-12 rounded object-cover" alt="room" />
                                    </td>
                                    <td class="border px-3 py-2">{{ room.room_number }}</td>
                                    <td class="border px-3 py-2">{{ room.category?.cat_name }}</td>
                                    <td class="border px-3 py-2">${{ Number(room.price).toFixed(2) }}</td>
                                    <td class="border px-3 py-2">{{ room.status }}</td>
                                    <td class="border px-3 py-2">
                                        <div v-if="isAdmin" class="flex gap-2">
                                            <button class="rounded bg-amber-500 px-2 py-1 text-white" @click="startEdit(room)">Edit</button>
                                            <button class="rounded bg-rose-600 px-2 py-1 text-white" @click="removeRoom(room.id)">Delete</button>
                                        </div>
                                        <div v-else>
                                            <div v-if="room.status !== 'ទំនេរ'" class="text-gray-500">មិនទំនេរ</div>
                                            <div v-else-if="!hasRequiredInfo" class="space-y-2">
                                                <p class="text-xs text-rose-600">សូមបំពេញព័ត៌មាននៅ About Me មុនកក់</p>
                                                <Link :href="route('about-me.index')" class="text-sm text-indigo-600 underline">ទៅ About Me</Link>
                                            </div>
                                            <form v-else class="flex items-center gap-2" @submit.prevent="bookRoom(room.id)">
                                                <input type="date" class="rounded-md border-gray-300 text-xs" v-model="getBookingForm(room.id).move_in_date" required />
                                                <button class="rounded bg-emerald-600 px-2 py-1 text-white" :disabled="getBookingForm(room.id).processing">កក់</button>
                                            </form>
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
