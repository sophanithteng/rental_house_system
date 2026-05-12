<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-lg bg-sky-600 p-5 text-white shadow">
                        <p class="text-sm"> Total Rooms</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.totalRooms }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-600 p-5 text-white shadow">
                        <p class="text-sm">Available Rooms</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.availableRooms }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-500 p-5 text-white shadow">
                        <p class="text-sm">Rented Rooms</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.rentedRooms }}</p>
                    </div>
                    <div class="rounded-lg bg-rose-600 p-5 text-white shadow">
                        <p class="text-sm">Pending Bookings</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.pendingBookings }}</p>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Quick Links</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <Link :href="route('rooms.index')" class="rounded-md border p-4 hover:bg-gray-50">Rooms</Link>
                        <Link :href="route('categories.index')" class="rounded-md border p-4 hover:bg-gray-50">Categories</Link>
                        <Link :href="route('about-me.index')" class="rounded-md border p-4 hover:bg-gray-50">About Me</Link>
                        <Link v-if="isAdmin" :href="route('bookings.index')" class="rounded-md border p-4 hover:bg-gray-50">Bookings</Link>
                        <Link v-if="isAdmin" :href="route('users.index')" class="rounded-md border p-4 hover:bg-gray-50">Users</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
