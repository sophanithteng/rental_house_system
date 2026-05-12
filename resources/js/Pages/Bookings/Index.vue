<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    bookings: { type: Array, required: true },
});

const approveForm = useForm({
    move_in_date: '',
});
const approvingId = ref(null);

const approve = (bookingId) => {
    approvingId.value = bookingId;
    approveForm.post(route('bookings.approve', bookingId), {
        onSuccess: () => {
            approveForm.reset();
            approvingId.value = null;
        },
    });
};

const reject = (bookingId) => {
    router.post(route('bookings.reject', bookingId));
};

const markMovedIn = (bookingId) => {
    router.post(route('bookings.moved-in', bookingId));
};
</script>

<template>
    <Head title="Bookings" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">ការកក់បន្ទប់</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-3 py-2 text-left">ID</th>
                                    <th class="border px-3 py-2 text-left">បន្ទប់</th>
                                    <th class="border px-3 py-2 text-left">អ្នកកក់</th>
                                    <th class="border px-3 py-2 text-left">ទូរស័ព្ទ</th>
                                    <th class="border px-3 py-2 text-left">ថ្ងៃចូលនៅ</th>
                                    <th class="border px-3 py-2 text-left">ស្ថានភាព</th>
                                    <th class="border px-3 py-2 text-left">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in bookings" :key="booking.id">
                                    <td class="border px-3 py-2">{{ booking.id }}</td>
                                    <td class="border px-3 py-2">{{ booking.room?.room_number }}</td>
                                    <td class="border px-3 py-2">{{ booking.user?.tenant?.full_name ?? booking.user?.username }}</td>
                                    <td class="border px-3 py-2">{{ booking.user?.tenant?.phone ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ booking.move_in_date ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ booking.status }}</td>
                                    <td class="border px-3 py-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <template v-if="booking.status === 'រង់ចាំ'">
                                                <input v-if="approvingId === booking.id" v-model="approveForm.move_in_date" type="date" class="rounded-md border-gray-300 text-xs" required />
                                                <button v-if="approvingId !== booking.id" class="rounded bg-emerald-600 px-2 py-1 text-white" @click="approvingId = booking.id">Approve</button>
                                                <button v-else class="rounded bg-emerald-700 px-2 py-1 text-white" @click="approve(booking.id)">Confirm</button>
                                                <button class="rounded bg-rose-600 px-2 py-1 text-white" @click="reject(booking.id)">Reject</button>
                                            </template>
                                            <button v-if="booking.status === 'បានអនុម័ត'" class="rounded bg-indigo-600 px-2 py-1 text-white" @click="markMovedIn(booking.id)">
                                                Mark moved in
                                            </button>
                                        </div>
                                        <p v-if="approveForm.errors.move_in_date && approvingId === booking.id" class="mt-1 text-xs text-rose-600">{{ approveForm.errors.move_in_date }}</p>
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
