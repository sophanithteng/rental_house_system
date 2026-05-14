<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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

const showGreeting = ref(true);
const currentTime = new Date().getHours();

const timeGreeting = computed(() => {
    if (currentTime < 12) return 'Good Morning';
    if (currentTime < 18) return 'Good Afternoon';
    return 'Good Evening';
});
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div v-if="showGreeting"
            class="relative mb-6 overflow-hidden rounded-xl bg-blue-50 p-6 text-blue-900 border border-blue-100 shadow-sm transition-all duration-300">
            <!-- Dismiss Button -->
            <button @click="showGreeting = false"
                class="absolute right-4 top-4 text-blue-400 hover:text-blue-600 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center gap-4">
                <img src="/images/greeting.png" class="h-12 w-12 shrink-0 rounded-lg object-cover" alt="Greeting" />
                <div>
                    <h5 class="text-lg font-bold">
                        {{ timeGreeting }}, {{ $page.props.auth.user.username }}!
                    </h5>
                    <p class="text-blue-700 mt-0.5">
                        Welcome back to the rental house management system. You can select the following actions to get
                        started.
                    </p>
                </div>
            </div>
        </div>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Top Statistic Cards -->
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

                    <!-- Rooms -->
                    <Link :href="route('rooms.index')"
                        class="group overflow-hidden rounded-lg bg-cyan-500 text-white shadow transition hover:scale-[1.02]">
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <h3 class="text-5xl font-bold">{{ stats.totalRooms }}</h3>
                                <p class="mt-2 text-lg">Rooms</p>
                            </div>

                            <svg class="h-20 w-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        </div>

                        <div
                            class="bg-black/10 py-2 text-center text-sm font-medium transition group-hover:bg-black/20">
                            See more →
                        </div>
                    </Link>

                    <!-- Categories -->
                    <Link :href="route('categories.index')"
                        class="group overflow-hidden rounded-lg bg-yellow-400 text-gray-900 shadow transition hover:scale-[1.02]">
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <h3 class="text-5xl font-bold">{{ stats.totalCategories ?? 'N/A' }}</h3>
                                <p class="mt-2 text-lg">Categories</p>
                            </div>

                            <svg class="h-20 w-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </div>

                        <div
                            class="bg-black/10 py-2 text-center text-sm font-medium transition group-hover:bg-black/20">
                            See more →
                        </div>
                    </Link>

                    <!-- Bookings -->
                    <Link v-if="isAdmin" :href="route('bookings.index')"
                        class="group overflow-hidden rounded-lg bg-green-500 text-white shadow transition hover:scale-[1.02]">
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <h3 class="text-5xl font-bold">{{ stats.pendingBookings }}</h3>
                                <p class="mt-2 text-lg">Pending Bookings</p>
                            </div>

                            <svg class="h-20 w-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <div
                            class="bg-black/10 py-2 text-center text-sm font-medium transition group-hover:bg-black/20">
                            See more →
                        </div>
                    </Link>

                    <!-- Users -->
                    <Link v-if="isAdmin" :href="route('users.index')"
                        class="group overflow-hidden rounded-lg bg-gray-500 text-white shadow transition hover:scale-[1.02]">
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <h3 class="text-5xl font-bold">{{ stats.totalUsers ?? 'N/A' }}</h3>
                                <p class="mt-2 text-lg">Users</p>
                            </div>

                            <svg class="h-20 w-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7" />
                            </svg>
                        </div>

                        <div
                            class="bg-black/10 py-2 text-center text-sm font-medium transition group-hover:bg-black/20">
                            See more →
                        </div>
                    </Link>
                </div>

                <!-- Quick Action Buttons -->
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">

                    <!-- About -->
                    <Link :href="route('about-me.index')"
                        class="rounded-2xl border border-gray-100 bg-gray-50 p-8 text-center transition hover:-translate-y-1 hover:shadow-md">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-200">
                             <img src="/images/about-me.png" class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                alt="Greeting" />
                        </div>

                        <h3 class="text-lg font-bold text-gray-800">About Me</h3>
                        <p class="mt-1 text-sm text-gray-500">Profile settings</p>
                    </Link>

                    <!-- Rooms -->
                    <Link :href="route('rooms.index')"
                        class="rounded-2xl border border-gray-100 bg-gray-50 p-8 text-center transition hover:-translate-y-1 hover:shadow-md">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-cyan-100">
                            <img src="/images/rooms.png" class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                alt="Greeting" />
                        </div>

                        <h3 class="text-lg font-bold text-cyan-600">Rooms</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage rooms</p>
                    </Link>

                    <!-- Categories -->
                    <Link :href="route('categories.index')"
                        class="rounded-2xl border border-gray-100 bg-gray-50 p-8 text-center transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                            <img src="/images/categories.png" class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                alt="Greeting" />
                        </div>

                        <h3 class="text-lg font-bold text-emerald-600">Categories</h3>
                        <p class="mt-1 text-sm text-gray-500">Room categories</p>
                    </Link>

                    <!-- Booking -->
                    <Link v-if="isAdmin" :href="route('bookings.index')"
                        class="rounded-2xl border border-gray-100 bg-gray-50 p-8 text-center transition hover:-translate-y-1 hover:shadow-md">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100">
                            <img src="/images/online-booking.png" class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                alt="Greeting" />
                        </div>

                        <h3 class="text-lg font-bold text-yellow-600">Bookings</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage bookings</p>
                    </Link>

                    <!-- Users -->
                    <Link v-if="isAdmin" :href="route('users.index')"
                        class="rounded-2xl border border-gray-100 bg-gray-50 p-8 text-center transition hover:-translate-y-1 hover:shadow-md">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-rose-100">
                            <font-awesome-icon :icon="['fas', 'users']" />
                        </div>

                        <h3 class="text-lg font-bold text-rose-600">Users</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage users</p>
                    </Link>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
