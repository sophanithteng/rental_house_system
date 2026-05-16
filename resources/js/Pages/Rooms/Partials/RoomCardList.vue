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

defineEmits(['edit', 'delete']);

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
    <div v-if="rooms.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div v-for="room in rooms" :key="room.id" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md hover:border-indigo-200">
            <!-- Image Container -->
            <div class="relative aspect-video overflow-hidden bg-slate-100">
                <img v-if="imageUrl(room.photo)" :src="imageUrl(room.photo)" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" :alt="room.room_number" />
                <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                    <i class="fas fa-image text-3xl"></i>
                </div>

                <!-- Status Badge Overlay -->
                <div class="absolute right-3 top-3">
                    <span v-if="room.status === 'available' || room.status === 'ទំនេរ'" class="inline-flex items-center rounded-full bg-emerald-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                        Available
                    </span>
                    <span v-else-if="room.status === 'booked'" class="inline-flex items-center rounded-full bg-amber-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                        Booked
                    </span>
                    <span v-else class="inline-flex items-center rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                        Occupied
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="flex flex-1 flex-col p-5">
                <div class="mb-1 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">{{ room.room_number }}</h3>
                    <span class="text-sm font-semibold text-indigo-600">${{ Number(room.price).toFixed(2) }}<span class="text-[10px] font-medium text-slate-400">/mo</span></span>
                </div>

                <div class="mb-4">
                    <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                        {{ room.category?.cat_name || 'General' }}
                    </span>
                </div>

                <p v-if="room.description" class="mb-4 line-clamp-2 text-sm text-slate-500 leading-relaxed">
                    {{ room.description }}
                </p>

                <!-- Footer Actions -->
                <div class="mt-auto pt-4 border-t border-slate-50">
                    <div v-if="isAdmin" class="flex gap-2">
                        <button @click="$emit('edit', room)" class="flex-1 rounded-lg bg-amber-50 py-2 text-xs font-bold text-amber-600 transition-colors hover:bg-amber-100">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <button @click="$emit('delete', room.id)" class="flex-1 rounded-lg bg-rose-50 py-2 text-xs font-bold text-rose-600 transition-colors hover:bg-rose-100">
                            <i class="fas fa-trash-alt mr-1"></i> Delete
                        </button>
                    </div>

                    <div v-else>
                        <div v-if="room.status !== 'available' && room.status !== 'ទំនេរ'" class="text-center text-xs font-medium text-slate-400 italic py-2">
                            Not available for booking
                        </div>
                        <div v-else-if="!hasRequiredInfo" class="text-center">
                            <Link :href="route('about-me.index')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                Update Profile to Book
                            </Link>
                        </div>
                        <form v-else class="flex items-center gap-2" @submit.prevent="bookRoom(room.id)">
                            <input
                                type="date"
                                v-model="getBookingForm(room.id).move_in_date"
                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <button :disabled="getBookingForm(room.id).processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-all disabled:opacity-50">
                                Reserve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20">
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-400">
            <i class="fas fa-search text-2xl"></i>
        </div>
        <p class="text-slate-500 font-medium">No rooms match your search.</p>
    </div>
</template>
