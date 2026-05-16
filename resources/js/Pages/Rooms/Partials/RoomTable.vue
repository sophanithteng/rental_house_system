<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    rooms: {
        type: Array,
        default: () => [],
    },
    isAdmin: Boolean,
    hasRequiredInfo: Boolean,
});

const emit = defineEmits(['edit', 'delete']);

const bookingForms = ref({});
const imageUrl = (path) => (path ? `/storage/${path}` : null);

const getBookingForm = (roomId) => {
    if (!bookingForms.value[roomId]) {
        bookingForms.value[roomId] = useForm({
            move_in_date: '',
        });
    }
    return bookingForms.value[roomId];
};

const bookRoom = (roomId) => {
    const form = getBookingForm(roomId);
    form.post(route('rooms.book', roomId));
};
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/70 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">Photo</th>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">Room Number</th>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">Category</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-semibold">Monthly Price</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-semibold">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    <tr v-for="room in rooms" :key="room.id" class="transition-colors hover:bg-slate-50/50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex h-12 w-14 shrink-0 items-center justify-center rounded-lg bg-slate-100 overflow-hidden border border-slate-100">
                                <img v-if="imageUrl(room.photo)" :src="imageUrl(room.photo)" class="h-full w-full object-cover" alt="Room graphic" />
                                <svg v-else class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75H21m-3.75 3.75H21" />
                                </svg>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">{{ room.room_number }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800 border border-slate-200">
                                {{ room.category?.cat_name || 'Unassigned' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-slate-900">${{ Number(room.price).toFixed(2) }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-center">
                            <span v-if="room.status === 'available' || room.status === 'ទំនេរ'" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Available
                            </span>
                            <span v-else-if="room.status === 'booked'" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Booked
                            </span>
                            <span v-else class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Occupied
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <div v-if="isAdmin" class="flex items-center justify-end gap-2">
                                <button class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-amber-600 transition-colors" @click="$emit('edit', room)">
                                    Edit
                                </button>
                                <button class="inline-flex items-center rounded-lg bg-white border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-600 shadow-sm hover:bg-rose-50 transition-colors" @click="$emit('delete', room.id)">
                                    Delete
                                </button>
                            </div>
                            <div v-else class="flex justify-end">
                                <div v-if="room.status !== 'available' && room.status !== 'ទំនេរ'" class="text-xs font-medium text-slate-400 italic">
                                    Unavailable
                                </div>
                                <div v-else-if="!hasRequiredInfo" class="text-right space-y-1">
                                    <p class="text-[11px] leading-tight text-rose-500 max-w-[180px]">Profile action needed before booking.</p>
                                    <Link :href="route('about-me.index')" class="inline-block text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                        Update Profile &rarr;
                                    </Link>
                                </div>
                                <form v-else class="flex items-center gap-2" @submit.prevent="bookRoom(room.id)">
                                    <input type="date" class="rounded-lg border-slate-200 py-1 px-2 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" v-model="getBookingForm(room.id).move_in_date" required />
                                    <button class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-all" :disabled="getBookingForm(room.id).processing">
                                        Reserve
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="rooms.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400 bg-slate-50/30">
                            No rooms found matching the current selections.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
